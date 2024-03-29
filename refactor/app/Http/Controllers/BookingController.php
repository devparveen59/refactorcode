<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;
    protected static $allowedAauthenticatedUserTypes = [];
    
    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
        self::setAllowedAuthenticatedUsers([
            env('ADMIN_ROLE_ID'),
            env('SUPERADMIN_ROLE_ID'),
        ]);
    }

    /**
     * Set the allowed authenticated user types.
     *
     * @param array $roles An array containing the roles to be added to the allowed types.
     * @return array The updated list of allowed authenticated user types.
     */
    public static function setAllowedAuthenticatedUsers(array $roles): array
    {
        self::$allowedAauthenticatedUserTypes = array_unique(
            array_merge(
                self::$allowedAauthenticatedUserTypes,
                $roles
            )
        );

        return self::$allowedAauthenticatedUserTypes;
    }

    /**
     * Fetch user jobs
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $user_id = $request->get('user_id');

        if ($user_id) {
            $response = $this->repository->getUsersJobs($user_id);
        } elseif (
            in_array(
                $request->__authenticatedUser->user_type,
                self::$allowedAauthenticatedUserTypes
            )
        ) {
            $response = $this->repository->getAll($request);
        } else {
            $response = null;
        }

        return response($response);
    }

    /**
     * To fetch job by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        return response($job);
    }

    /**
     * To store data
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->store($request->__authenticatedUser, $data);

        return response($response);
    }

    /**
     * To update job
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $data = $request->except(['_token','submit']);
        $cuser = $request->__authenticatedUser;
        $response = $this->repository->updateJob($id, $data, $cuser);

        return response($response);
    }

    /**
     * Immediate job email
     *
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->storeJobEmail($data);
        
        return response($response);
    }

    /**
     * Fetch History
     *
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        $user_id = $request->get('user_id');
        if ($user_id) {
            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response($response);
        }

        return null;
    }

    /**
     * Accept job
     *
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($data, $user);

        return response($response);
    }

    /**
     * Accept job with id
     *
     * @param Request $request
     * @return mixed
     */
    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;
        $response = $this->repository->acceptJobWithId($data, $user);

        return response($response);
    }

    /**
     * To cancel job
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;
        $response = $this->repository->cancelJobAjax($data, $user);

        return response($response);
    }

    /**
     * To end job
     *
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();

        $response = $this->repository->endJob($data);

        return response($response);
    }

    /**
     * To fetch data for customer not call
     *
     * @param Request $request
     * @return mixed
     */
    public function customerNotCall(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->customerNotCall($data);

        return response($response);
    }

    /**
     * To fetch potential jobs
     *
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        $user = $request->__authenticatedUser;
        $response = $this->repository->getPotentialJobs($user);

        return response($response);
    }

    /**
     * To update distance information
     *
     * @param Request $request
     * @return \Illuminate\Http\Response The response indicating the result of the distance update action.
     */

    public function distanceFeed(Request $request)
    {
        $data = $request->all();
        $distance = $data['distance'] ?? '';
        $time = $data['time'] ?? '';
        $jobid = $data['jobid'] ?? '';
        $session = $data['session_time'] ?? '';
        $flagged = ($data['flagged'] == 'true') ? 'yes' : 'no';
        $manually_handled = $data['manually_handled'] === 'true' ? 'yes' : 'no';
        $by_admin = $data['by_admin'] === 'true' ? 'yes' : 'no';
        $admincomment = $data['admincomment'] ?? '';

        if ($data['admincomment'] == '') {
            return response(['error' => 'Please, add comment'], 400);
        }
    
        if ($time || $distance) {
            Distance::where('job_id', '=', $jobid)
            ->update(['distance' => $distance, 'time' => $time]);
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', '=', $jobid)
            ->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin
            ]);
        }

        return response(['success' => 'Record updated!'], 200);
    }

    /**
     * Reopen job
     *
     * @param Request $request
     * @return \Illuminate\Http\Response The response indicating the result of the reopening operation.
     */

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->reopen($data);

        return response($response);
    }
    /**
     * Resend Notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\Response The response indicating the result of the resend notification
     */

    public function resendNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        if (!$job) {
            return response(['error' => 'Job not found'], 404);
        }
        
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent'], 200);
    }

    /**
     * Sends SMS to Translator
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        
        if (!$job) {
            return response(['error' => 'Job not found'], 404);
        }
    
        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 500);
        }
    }
}
