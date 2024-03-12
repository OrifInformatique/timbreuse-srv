<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventSeriesModel;

class EventSeries extends BaseController
{
    // Class properties
    private EventSeriesModel $eventSeriesModel;

    /**
     * Constructor
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        // Set Access level before calling parent constructor
        // Accessibility reserved to registered users
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_registered;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required models
        $this->eventSeriesModel = new EventSeriesModel();
    }

    public function getCreateSeriesHTML() : string {
        $data = [
            'daysOfWeek' => [
                lang('tim_lang.monday'),
                lang('tim_lang.tuesday'),
                lang('tim_lang.wednesday'),
                lang('tim_lang.thursday'),
                lang('tim_lang.friday')
            ],
            'eventSerie' => null
        ];

        return json_encode(view('\Timbreuse\Views\eventSeries\create_series.php', $data));
    }
}