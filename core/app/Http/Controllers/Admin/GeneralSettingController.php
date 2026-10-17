<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactAdmin;
use App\Models\OfficialChannel;
use App\Models\Frontend;
use App\Models\Plan;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Image;

class GeneralSettingController extends Controller
{
    public function index()
    {
        $pageTitle = 'General Setting';
        $timezones = json_decode(file_get_contents(resource_path('views/admin/partials/timezone.json')));
        $plans = Plan::where('status', 1)->get();
        return view('admin.setting.general', compact('pageTitle', 'timezones', 'plans'));
    }

    public function contactLinks()
    {
        ensureOfficialChannelsTable();
        $pageTitle = 'Official Channels';
        $general = gs();
        $channels = officialChannels(false);
        return view('admin.setting.contact-links', compact('pageTitle', 'general', 'channels'));
    }

    public function channelStore(Request $request)
    {
        ensureOfficialChannelsTable();
        $data = $this->channelData($request);
        if ($data['sort_order'] === null) {
            $data['sort_order'] = ((int) OfficialChannel::max('sort_order')) + 1;
        }
        OfficialChannel::create($data);

        $notify[] = ['success', 'Channel added successfully'];
        return back()->withNotify($notify);
    }

    public function channelUpdate(Request $request, $id)
    {
        ensureOfficialChannelsTable();
        $channel = OfficialChannel::findOrFail($id);
        $data = $this->channelData($request, $channel);
        $channel->update($data);

        $notify[] = ['success', 'Channel updated successfully'];
        return back()->withNotify($notify);
    }

    public function channelDelete($id)
    {
        ensureOfficialChannelsTable();
        $channel = OfficialChannel::findOrFail($id);
        if ($channel->image) {
            fileManager()->removeFile(public_path(getFilePath('channel') . '/' . $channel->image));
        }
        $channel->delete();

        $notify[] = ['success', 'Channel removed successfully'];
        return back()->withNotify($notify);
    }

    public function channelStatus($id)
    {
        ensureOfficialChannelsTable();
        $channel = OfficialChannel::findOrFail($id);
        $channel->status = !$channel->status;
        $channel->save();

        $notify[] = ['success', $channel->status ? 'Channel enabled successfully' : 'Channel disabled successfully'];
        return back()->withNotify($notify);
    }

    protected function channelData(Request $request, ?OfficialChannel $channel = null): array
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'purpose' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'link' => 'required|url|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $image = $channel?->image;
        if ($request->hasFile('image')) {
            $image = fileUploader($request->file('image'), getFilePath('channel'), null, $image);
        }

        return [
            'title' => trim($request->title),
            'purpose' => trim((string) $request->purpose) ?: null,
            'description' => trim((string) $request->description) ?: null,
            'link' => trim($request->link),
            'icon' => $channel?->icon,
            'image' => $image,
            'status' => (int) $request->status,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : $channel?->sort_order,
        ];
    }

    public function contactLinksUpdate(Request $request)
    {
        $request->validate([
            'whatsapp_community_link' => 'nullable|string|max:500',
            'whatsapp_channel_link' => 'nullable|string|max:500',
        ]);

        $general = gs();
        $general->whatsapp_community_link = trim((string) $request->whatsapp_community_link) ?: null;
        $general->whatsapp_channel_link = trim((string) $request->whatsapp_channel_link) ?: null;
        $general->save();

        \Illuminate\Support\Facades\Cache::forget('GeneralSetting');

        $notify[] = ['success', 'Contact links updated successfully'];
        return back()->withNotify($notify);
    }

    public function contactAdmins()
    {
        ensureContactAdminsTable();
        $pageTitle = 'Contact Us Admins';
        $admins = ContactAdmin::orderBy('id')->get();

        return view('admin.setting.contact-admins', compact('pageTitle', 'admins'));
    }

    public function contactAdminStore(Request $request)
    {
        ensureContactAdminsTable();
        $data = $this->contactAdminData($request);
        ContactAdmin::create($data);

        $notify[] = ['success', 'Contact admin added successfully'];
        return back()->withNotify($notify);
    }

    public function contactAdminUpdate(Request $request, $id)
    {
        ensureContactAdminsTable();
        $admin = ContactAdmin::findOrFail($id);
        $admin->update($this->contactAdminData($request));

        $notify[] = ['success', 'Contact admin updated successfully'];
        return back()->withNotify($notify);
    }

    public function contactAdminDelete($id)
    {
        ensureContactAdminsTable();
        ContactAdmin::findOrFail($id)->delete();

        $notify[] = ['success', 'Contact admin removed successfully'];
        return back()->withNotify($notify);
    }

    protected function contactAdminData(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|max:150',
            'instructions' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:40',
            'whatsapp_number' => 'nullable|string|max:40',
            'bot_link' => 'nullable|string|max:500',
            'contact_type' => 'required|in:whatsapp,botim',
        ]);

        $contactNumber = trim((string) $request->contact_number);
        $whatsappNumber = trim((string) $request->whatsapp_number);
        $botLink = trim((string) $request->bot_link);

        if ($request->contact_type === 'botim' && $botLink === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bot_link' => 'Enter the Botim link or Botim number.',
            ]);
        }

        if ($request->contact_type === 'whatsapp' && $contactNumber === '' && $whatsappNumber === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'whatsapp_number' => 'Enter a WhatsApp number.',
            ]);
        }

        return [
            'name' => trim($request->name),
            'category' => trim($request->category),
            'instructions' => trim((string) $request->instructions) ?: null,
            'contact_number' => $contactNumber ?: null,
            'whatsapp_number' => $whatsappNumber ?: null,
            'bot_link' => $botLink ?: null,
            'contact_type' => $request->contact_type,
        ];
    }

    public function levels()
    {
        $pageTitle = "Package Levels Management";
        $plans = Plan::where('status', 1)->get();
        return view('admin.setting.level-system', compact('pageTitle', 'plans'));
    }
    
    public function getLevels(Request $request)
    {
        $plan = Plan::where('id', $request->plan)->first();
        $levels = persistCanonicalPlanLevels($plan);

        return response()->json([
            'levels' => $levels
        ]);
    }
    
    public function updateLevels(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);
    
        $plan = Plan::findOrFail($request->plan_id);
        $levels = normalizePlanLevels($request->levels);
        $plan->update([
            'levels' => $levels
        ]);
        
        updateUsersLevelByPlan($plan->id);
    
        return response()->json([
            'success' => true,
            'message' => 'Levels updated successfully'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:40',
            'cur_text' => 'required|string|max:40',
            'cur_sym' => 'required|string|max:40',
            'base_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'secondary_color' => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'registration_bonus' => 'required|gte:0',
            'bt_fixed' => 'required|gte:0',
            'bt_percent' => 'required|gte:0',
            'default_plan' => 'required',
            'timezone' => 'required',
        ]);

        $defaultPlan = $request->default_plan;
        if ($defaultPlan > 0) {
            $plan = Plan::where('status', 1)->findOrFail($defaultPlan);
            $defaultPlan = $plan->id;
        }

        $general = gs();
        $general->site_name = $request->site_name;
        $general->cur_text = $request->cur_text;
        $general->cur_sym = $request->cur_sym;
        $general->base_color = $request->base_color;
        $general->secondary_color = $request->secondary_color;
        $general->registration_bonus = $request->registration_bonus;
        $general->bt_fixed = $request->bt_fixed;
        $general->bt_percent = $request->bt_percent;
        $general->default_plan = $defaultPlan;
        $general->save();

        \Illuminate\Support\Facades\Cache::forget('GeneralSetting');

        $timezone = (string) $request->timezone;
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            $notify[] = ['error', 'Please choose a valid timezone.'];
            return back()->withNotify($notify);
        }

        $timezoneFile = config_path('timezone.php');
        $content = '<?php $timezone = ' . var_export($timezone, true) . ';' . PHP_EOL;
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration()
    {
        $pageTitle = 'System Configuration';
        return view('admin.setting.configuration', compact('pageTitle'));
    }

    public function systemConfigurationSubmit(Request $request)
    {
        $general = gs();
        $general->kv = $request->kv ? 1 : 0;
        $general->ev = $request->ev ? 1 : 0;
        $general->en = $request->en ? 1 : 0;
        $general->sv = $request->sv ? 1 : 0;
        $general->sn = $request->sn ? 1 : 0;
        $general->force_ssl = $request->force_ssl ? 1 : 0;
        $general->secure_password = $request->secure_password ? 1 : 0;
        $general->registration = $request->registration ? 1 : 0;
        $general->agree = $request->agree ? 1 : 0;
        $general->balance_transfer = $request->balance_transfer ? 1 : 0;
        $general->save();
        $notify[] = ['success', 'System configuration updated successfully'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'favicon' => ['image', new FileTypeValidate(['png'])],
        ]);
        if ($request->hasFile('logo')) {
            try {
                $path = getFilePath('logoIcon');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                Image::make($request->logo)->save($path . '/logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                $path = getFilePath('logoIcon');
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $size = explode('x', getFileSize('favicon'));
                Image::make($request->favicon)->resize($size[0], $size[1])->save($path . '/favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    public function customCss()
    {
        $pageTitle = 'Custom CSS';
        $file = $this->customCssFile();
        $file_content = is_file($file) ? file_get_contents($file) : '';
        return view('admin.setting.custom_css', compact('pageTitle', 'file_content'));
    }


    public function customCssSubmit(Request $request)
    {
        $file = $this->customCssFile();
        file_put_contents($file, (string) $request->css);
        $notify[] = ['success', 'CSS updated successfully'];
        return back()->withNotify($notify);
    }

    protected function customCssFile(): string
    {
        $template = basename((string) (gs()->active_template ?: 'basic'));
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $template)) {
            $template = 'basic';
        }

        $directory = public_path('assets/templates/' . $template . '/css');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return $directory . DIRECTORY_SEPARATOR . 'custom.css';
    }

    public function maintenanceMode()
    {
        $pageTitle = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance', compact('pageTitle', 'maintenance'));
    }

    public function maintenanceModeSubmit(Request $request)
    {
        $request->validate([
            'description' => 'required'
        ]);
        $general = gs();
        $general->maintenance_mode = $request->status ? 1 : 0;
        $general->save();

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        $maintenance->data_values = [
            'description' => $request->description,
        ];
        $maintenance->save();

        $notify[] = ['success', 'Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function cookie()
    {
        $pageTitle = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        return view('admin.setting.cookie', compact('pageTitle', 'cookie'));
    }

    public function cookieSubmit(Request $request)
    {
        $request->validate([
            'short_desc' => 'required|string|max:255',
            'description' => 'required',
        ]);
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc' => $request->short_desc,
            'description' => $request->description,
            'status' => $request->status ? 1 : 0,
        ];
        $cookie->save();
        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }

    public function adsSetting()
    {
        $pageTitle = 'Advertisements Setting';
        return view('admin.setting.ads', compact('pageTitle'));
    }

    public function adsSettingSubmit(Request $request)
    {
        $general = gs();
        $general->ads_setting = [
            'ad_price' => $request->ad_price,
            'amount_for_user' => $request->amount_for_user,
        ];
        $general->user_ads_post = $request->user_ads_post ? 1 : 0;
        $general->ad_auto_approve = $request->ad_auto_approve ? 1 : 0;
        $general->save();

        $notify[] = ['success', 'Ads setting updated successfully'];
        return back()->withNotify($notify);
    }
}
