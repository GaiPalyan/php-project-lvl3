<?php

declare(strict_types=1);

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class DomainRepository
{
    public function getList()
    {
        $domains = DB::table('urls')
            ->distinct()
            ->get()
            ->sortBy('id');
        $lastChecks = DB::table('url_checks')
            ->select('url_id', 'status_code', DB::raw('max(updated_at) as last_check'))
            ->groupBy('url_id', 'status_code')
            ->get()
            ->keyBy('url_id');
        return view('domains.show', compact('domains', 'lastChecks'));
    }

    public function getPage(int $id)
    {
        $domain = $this->getDomain($id);
        if (!$domain) {
            return abort(404);
        }

        $domainChecks = DB::table('url_checks')->where('url_id', '=', $id)
            ->paginate(10);

        return view('domains.domain', compact('domain', 'domainChecks'));
    }

    public function getDomain(int $id)
    {
        return DB::table('urls')->find($id);
    }

    public function saveDomain(array $domain)
    {
        DB::table('urls')->insert($domain);
    }

    public function updateDomainParam(int $id, string $column, string|int $value)
    {
        DB::table('urls')->where('id', $id)->update([$column => $value]);
    }
    public function saveDomainCheck(array $data)
    {
        try {
            DB::table('url_checks')->insert($data);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}