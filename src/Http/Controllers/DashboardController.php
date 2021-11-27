<?php

namespace Dashboard\Http\Controllers;

use Meta;
use Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Coderello\SharedData\Facades\SharedData;
use Spatie\Analytics\AnalyticsFacade as Analytics;
use Spatie\Analytics\Period;
use Dashboard\Models\SettingGroup;

class DashboardController extends Controller
{
    /**
     * Dashboard home page
     *
     * @return Illuminate\Support\Facades\View
     */
    public function index()
    {
        Meta::set('title', 'Панель управления');
        SharedData::put([
            // 'settings' => $this->getSettingsGroups(),
        ]);

        return view('dashboard::pages.dashboard');
    }

    /**
     * Fetch analytic data
     *
     * @param Illuminate\Http\Request $request
     * @return array Rest response
     */
    public function getAnalyticData(Request $request)
    {
        $payload = $this->validate($request, [
            'from' => ['sometimes', 'nullable', 'date'],
            'to' => ['sometimes', 'nullable', 'required_with:from', 'date'],
        ]);
        $dateFrom = $request->filled('from') ? Carbon::parse($request->input('from')) : Carbon::now()->subDays(30);
        $dateTo = $request->filled('to') ? Carbon::parse($request->input('to')) : Carbon::now();
        $period = Period::create($dateFrom, $dateTo);

        $result = [
            'visitorsAndPageViews' => $this->getVisitorsAndPageViews($period),
            'mostVisitedPages' => $this->getMostVisitedPages($period, 10),
            'topReferrers' => $this->getTopReferrers($period, 6),
            'userTypes' => $this->getUserTypes($period, 4),
            'devices' => $this->getDeviceVisitors($period),
            'period' => [
                'from' => $dateFrom->format('d.m.Y'),
                'to' => $dateTo->format('d.m.Y'),
            ],
        ];

        return $this->response([
            DATA => $result,
        ]);
    }

    /**
     * Get the amount of visitors and pageviews
     *
     * @param Spatie\Analytics\Period $period
     * @param string $groupBy [date|yearMonth]
     * @return array
     */
    public function getVisitorsAndPageViews(Period $period, $groupBy = 'date')
    {
        $startDate = $period->startDate->format('Y-m-d');
        $endDate = $period->endDate->format('Y-m-d');
        $cacheKeyName = "visitorsAndPageViews-$startDate-$endDate-$groupBy";
        return Cache::remember($cacheKeyName, $seconds = 60 * 60 * 24, function() use ($period, $groupBy) {
            try {
                $result = Analytics::fetchTotalVisitorsAndPageViews($period, $groupBy);
                return $result->map(function($item, $key) {
                    $item['date'] = $item['date']->format('d.m.Y');
                    return $item;
                });
            } catch(\Exception $exception) {
                return null;
            }
        });
    }

    /**
     * Get the most visited pages
     *
     * @param Spatie\Analytics\Period $period
     * @param int $maxResults
     * @return array
     */
    public function getMostVisitedPages(Period $period, $maxResults = 20)
    {
        $startDate = $period->startDate->format('Y-m-d');
        $endDate = $period->endDate->format('Y-m-d');
        $cacheKeyName = "mostVisitedPages-$startDate-$endDate-$maxResults";
        return Cache::remember($cacheKeyName, $seconds = 60 * 60 * 24, function() use ($period, $maxResults) {
            try {
                return Analytics::fetchMostVisitedPages($period, $maxResults);
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    /**
     * Get the top referrers
     *
     * @param Spatie\Analytics\Period $period
     * @param int $maxResults
     * @return array
     */
    public function getTopReferrers(Period $period, $maxResults = 20)
    {
        $startDate = $period->startDate->format('Y-m-d');
        $endDate = $period->endDate->format('Y-m-d');
        $cacheKeyName = "topReferrers-$startDate-$endDate-$maxResults";
        return Cache::remember($cacheKeyName, $seconds = 60 * 60 * 24, function() use ($period, $maxResults) {
            try {
                return Analytics::fetchTopReferrers($period)->take($maxResults);
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    /**
     * Get the user types
     *
     * @param Spatie\Analytics\Period $period
     * @param int $maxResults
     * @return array
     */
    public function getUserTypes(Period $period, $maxResults = 20)
    {
        $startDate = $period->startDate->format('Y-m-d');
        $endDate = $period->endDate->format('Y-m-d');
        $cacheKeyName = "userTypes-$startDate-$endDate-$maxResults";
        return Cache::remember($cacheKeyName, $seconds = 60 * 60 * 24, function() use($period, $maxResults) {
            try {
                return Analytics::fetchUserTypes($period)->take($maxResults);
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    /**
     * Get the device visitors
     *
     * @param Spatie\Analytics\Period $period
     * @return array
     */
    private function getDeviceVisitors(Period $period)
    {
        $startDate = $period->startDate->format('Y-m-d');
        $endDate = $period->endDate->format('Y-m-d');
        $cacheKeyName = "deviceVisitors-$startDate-$endDate";
        return Cache::remember($cacheKeyName, $seconds = 60 * 60 * 24, function() use($period) {
            try {
                $response = Analytics::performQuery(
                    $period, 'ga:users',
                    ['dimensions' => 'ga:deviceCategory'],
                );
                return collect($response['rows'] ?? [])->map(function(array $row) {
                    return [
                        'type' => $row[0],
                        'sessions' => (int) $row[1],
                    ];
                });
            } catch (\Exception $exception) {
                return null;
            }
        });
    }
}
