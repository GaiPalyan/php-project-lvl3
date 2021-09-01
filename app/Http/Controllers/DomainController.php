<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\DomainManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    private DomainManager $manager;

    /**
     * @param DomainManager $manager
     */
    public function __construct(DomainManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Show domains main list
     */
    public function show()
    {
        return $this->manager->getDomainsList();
    }

    /**
     * Show domain personal page
     *
     * @param int $id
     */
    public function domainPage(int $id)
    {
        return $this->manager->getDomainPersonalPage($id);
    }

    /**
     * Validate and store domain
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
       // dd($request->input('url'));
        $validateDomain = Validator::make(
            $request->input('url'),
            ['name' => 'required|url|max:255'],
            $messages = [
                'required' => 'Поле ввода не может быть пустым',
                'url' => 'Некорректный адрес',
                'max' => 'Максимальная допустимая длина адреса 255 символов',
            ]
        );

        if ($validateDomain->fails()) {
            flash($validateDomain->errors()->first('name'))->error()->important();
            return redirect()->route('domains.create');
        }

        $requestData = $request->toArray();
        $name = $requestData['url']['name'];
        $existDomain = $this->manager->getDomainInfo($name);

        if ($existDomain) {
            flash('Адрес уже существует')->info()->important();
            return  redirect()->route('domain_personal_page.show', $existDomain);
        }

        $this->manager->prepareBasicDomainData($name);
        flash('Адрес добавлен в базу!')->success()->important();

        $assignedId = $this->manager->getDomainInfo($name);
        return redirect()->route('domain_personal_page.show', $assignedId);
    }

    /**
     * Store domain parsing result
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \DiDom\Exceptions\InvalidSelectorException
     */
    public function storeCheck(int $id): \Illuminate\Http\RedirectResponse
    {
        $this->manager->prepareDomainCheckData($id);
        flash('Проверка прошла успешно')->success()->important();
        return redirect()->route('domain_personal_page.show', $id);
    }

    public function create()
    {
        return view('domains.create');
    }
}
