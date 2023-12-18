<?php

namespace Axistrustee\Covenants;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;

class Covenants extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('covenants', __DIR__.'/../dist/js/tool.js');
        Nova::style('covenants', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        $user_object = \Auth::user();
        $user_role = $user_object->role->role;
        $menu = [];
        if($user_role == config('global.roles.CSOG_CHECKER') || $user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.SUPER_ADMIN')) {
           $menu[] = MenuSection::make('Pending For Approval')->path( '/covenants/pending-approval')->icon('server');
        }
        else if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.SUPER_ADMIN')) {
           $menu[] = MenuSection::make('Approved Covenants')->path( '/covenants/approved-list')->icon('server');
        }
        //return $menu;
        $menu[] = MenuSection::make('List Of Covenants - Active')->path( '/covenants/summary')->icon('server');
        if($user_role == config('global.roles.CSOG_CHECKER') || $user_role == config('global.roles.CCU_CHECKER') || $user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.SUPER_ADMIN')) {
           $menu[] = MenuSection::make('Pending For Approval - Active')->path( '/covenants/pending-approval-active')->icon('server');
        }
        else if($user_role == config('global.roles.AUDITOR') || $user_role == config('global.roles.ADMIN') || $user_role == config('global.roles.SUPER_ADMIN')) {
           $menu[] = MenuSection::make('Approved - Active')->path( '/covenants/approved-active')->icon('server');
        }
        return $menu;
        
    }
}
