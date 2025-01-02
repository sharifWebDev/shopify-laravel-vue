<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Template;
use App\Models\ShopifyStore;
use Illuminate\Http\Request;
use App\Models\StoreTemplate;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * @param Request $request
     */
    public function simpleBar(Request $request)
    {

        // dd($dd);

        $type       = "simple";
        $templateID = Template::where('type', $type)->firstOrFail()->id;
        $storeName  = Auth::user()->myshopify_domain;

        return Inertia::render('AnnouncementBar', [
            'type'       => $type,
            'templateID' => $templateID,
            'storeName'  => $storeName,
        ]);
    }

    public function timerBar()
    {
        $type       = "timer";
        $templateID = Template::where('type', $type)->firstOrFail()->id;
        $storeName  = Auth::user()->myshopify_domain;

        return Inertia::render('AnnouncementBar', [
            'type'       => $type,
            'templateID' => $templateID,
            'storeName'  => $storeName,
        ]);
    }

    public function marqueeBar()
    {
        $type       = "marquee";
        $templateID = Template::where('type', $type)->firstOrFail()->id;
        $storeName  = Auth::user()->myshopify_domain;

        return Inertia::render('AnnouncementBar', [
            'type'       => $type,
            'templateID' => $templateID,
            'storeName'  => $storeName,
        ]);
    }

    /**
     * @param StoreTemplate $storeTemplate
     */
    public function edit(StoreTemplate $storeTemplate)
    {

        $type      = $storeTemplate->template->type;
        $storeName = Auth::user()->myshopify_domain;

     
            return Inertia::render('AnnouncementBar', [
                'type'       => $type,
                'templateID' => $storeTemplate->id,
                'storeName'  => $storeName,
                'data'       => $storeTemplate->data,
            ]);


        if ($type == "timer") {
            return Inertia::render('Template/TimerBar', [
                'type'       => $type,
                'templateID' => $storeTemplate->id,
                'storeName'  => $storeName,
            ]);
        }
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {

        // dd( $request->all(), $request->startDate . ' ' . $request->startTime );

        // dd($request->all());

        $request->validate([
            'announcementName'    => 'required',
            'announcementContent' => 'required',
            'selectedStatus'      => 'required',
            'storeName'           => 'required',
            'selectedDevice'      => 'required',
            'device'              => 'required',
            'page'                => 'required',
            'barPosition'         => 'required',
        ]);

        // dd($request->storeName);

        $shopifyStore      = ShopifyStore::where('myshopify_domain', $request->storeName)->firstOrFail();
        $deductFormRequest = ['templateID', 'storeName'];
        $simpleBarData     = $request->except($deductFormRequest);
        $startDateAndTime  = $request->startDate . ' ' . $request->startTime ?? now();
        $endDateAndTime    = $request->startDate . ' ' . $request->startTime ?? null;

        $storeTemplateData['template_id']      = $request->templateID;
        $storeTemplateData['shopify_store_id'] = $shopifyStore->id;
        $storeTemplateData['created_by']       = Auth::id();
        $storeTemplateData['name']             = $request->announcementName;
        $storeTemplateData['message']          = $request->announcementContent;
        $storeTemplateData['start_at']         = Carbon::parse($startDateAndTime);
        $storeTemplateData['expired_at']       = Carbon::parse($endDateAndTime);
        $storeTemplateData['is_active']        = $request->selectedStatus == 'draft' ? false : true;
        $storeTemplateData['data']             = $simpleBarData;

        $storeTemplate = StoreTemplate::create($storeTemplateData);

        return to_route('dashboard')->with('success', 'Store Template created successfully.');
    }

    /**
     * @param Request       $request
     * @param StoreTemplate $storeTemplate
     */
    public function update(Request $request, StoreTemplate $storeTemplate)
    {

        // dd($request->all());

        $deductFormRequest = ['templateID', 'storeName'];
        $simpleBarData     = $request->except($deductFormRequest);

        $storeTemplateData['name']       = $request->announcementName;
        $storeTemplateData['message']    = $request->announcementContent;
        $storeTemplateData['start_at']   = Carbon::parse($request->startDate) ?? now();
        $storeTemplateData['expired_at'] = Carbon::parse($request->startTime);
        $storeTemplateData['is_active']  = $request->selectedStatus == 'draft' ? false : true;
        $storeTemplateData['data']       = $simpleBarData;

        $storeTemplate = $storeTemplate->update($storeTemplateData);

        return to_route('dashboard')->with('success', 'Store Template created successfully.');
    }

    /**
     * @param $id
     */
    public function destroy($id)
    {
        $storeTemplate = StoreTemplate::findOrFail($id);
        $storeTemplate->delete();

        return to_route('dashboard')->with('success', 'Template deleted successfully!.');
    }

    /**
     * @param Request $request
     */
    public function templateBulkDestroy(Request $request)
    {

        if ($request->has('ids') && $request->ids) {

            foreach ($request->ids as $id) {
                $storeTemplate = StoreTemplate::first('id', $id);
                if ($storeTemplate) {
                    $storeTemplate->delete();
                }
            }
        }
        return to_route('dashboard')->with('success', 'Template deleted successfully!.');
    }
}
