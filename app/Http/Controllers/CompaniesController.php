<?php
namespace App\Http\Controllers;

use App\Models\Company;
use Input;
use Lang;
use Redirect;
use View;
use Illuminate\Http\Request;

/**
 * This controller handles all actions related to Companies for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */

final class CompaniesController extends Controller
{

    /**
    * Returns view to display listing of companies.
    *
    * @author [Abdullah Alansari] [<ahimta@gmail.com>]
    * @since [v1.8]
    * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('companies/index')->with('companies', Company::all());
    }

    /**
    * Returns view to create a new company.
    *
    * @author [Abdullah Alansari] [<ahimta@gmail.com>]
    * @since [v1.8]
    * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('companies/edit')->with('item', new Company);
    }

    /**
     * Save data from new company form.
     *
     * @author [Abdullah Alansari] [<ahimta@gmail.com>]
     * @since [v1.8]
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->input('name');

        if ($company->save()) {
            return redirect()->route('companies.index')
                ->with('success', trans('admin/companies/message.create.success'));
        }
        return redirect()->back()->withInput()->withErrors($company->getErrors());
    }


    /**
    * Return form to edit existing company.
    *
    * @author [Abdullah Alansari] [<ahimta@gmail.com>]
    * @since [v1.8]
    * @param int $companyId
    * @return \Illuminate\Contracts\View\View
     */
    public function edit($companyId)
    {
        if (is_null($item = Company::find($companyId))) {
            return redirect()->route('companies.index')
                ->with('error', trans('admin/companies/message.does_not_exist'));
        }
        return view('companies/edit')->with('item', $item);
    }

    /**
     * Save data from edit company form.
     *
     * @author [Abdullah Alansari] [<ahimta@gmail.com>]
     * @since [v1.8]
     * @param Request $request
     * @param int $companyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $companyId)
    {
        if (is_null($company = Company::find($companyId))) {
            return redirect()->route('companies.index')->with('error', trans('admin/companies/message.does_not_exist'));
        }

        $company->name = $request->input('name');

        if ($company->save()) {
            return redirect()->route('companies.index')
                ->with('success', trans('admin/companies/message.update.success'));
        }
        return redirect()->route('companies.edit', ['company' => $companyId])
            ->with('error', trans('admin/companies/message.update.error'));
    }

    /**
    * Delete company
    *
    * @author [Abdullah Alansari] [<ahimta@gmail.com>]
    * @since [v1.8]
    * @param int $companyId
    * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($companyId)
    {
        if (is_null($company = Company::find($companyId))) {
            return redirect()->route('companies.index')
                ->with('error', trans('admin/companies/message.not_found'));
        } else {
            try {
                $company->delete();
                return redirect()->route('companies.index')
                    ->with('success', trans('admin/companies/message.delete.success'));
            } catch (\Illuminate\Database\QueryException $exception) {
            /*
                 * NOTE: This happens when there's a foreign key constraint violation
                 * For example when rows in other tables are referencing this company
                 */
                if ($exception->getCode() == 23000) {
                    return redirect()->route('companies.index')
                        ->with('error', trans('admin/companies/message.assoc_users'));
                } else {
                    throw $exception;
                }
            }
        }
    }

    public function show($id) {
        $this->authorize('view', Company::class);

        if (is_null($company = Company::find($id))) {
            return redirect()->route('companies.index')
                ->with('error', trans('admin/companies/message.not_found'));
        } else {
            return view('companies/view')->with('company',$company);
        }

    }
}
