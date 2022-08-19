<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DetailTask\BulkDestroyDetailTask;
use App\Http\Requests\Admin\DetailTask\DestroyDetailTask;
use App\Http\Requests\Admin\DetailTask\IndexDetailTask;
use App\Http\Requests\Admin\DetailTask\StoreDetailTask;
use App\Http\Requests\Admin\DetailTask\UpdateDetailTask;
use App\Models\DetailTask;
use App\Models\State;
use App\Models\Task;
use App\Models\AdminUser;



use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DetailTasksController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDetailTask $request
     * @return array|Factory|View
     */
    public function index(IndexDetailTask $request)
    {
        // create and AdminListing instance for a specific model and
      $data = AdminListing::create(DetailTask::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'task_id', 'state_id', 'date_begin', 'date_end', 'user_id', 'advance'],

            // set columns to searchIn
            ['id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.detail-task.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.detail-task.create');

        $state = State::all();
        $task = Task::all();
        $user = AdminUser::all();



        return view('admin.detail-task.create',compact('state','task','user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDetailTask $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDetailTask $request)
    {

         // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized ['state_id']=  $request->getStateId();
       $sanitized ['task_id']=  $request->getTaskId();
        $sanitized ['user_id']=  $request->getUserId();
        $detailTask = DetailTask::create($sanitized);
     if ($request->ajax()) {
            return ['redirect' => url('admin/detail-tasks'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/detail-tasks');
    }

    /**
     * Display the specified resource.
     *
     * @param DetailTask $detailTask
     * @throws AuthorizationException
     * @return void
     */
    public function show(DetailTask $detailTask)
    {
        $this->authorize('admin.detail-task.show', $detailTask);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DetailTask $detailTask
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DetailTask $detailTask)
    {
        $this->authorize('admin.detail-task.edit', $detailTask);

        $state = State::all();
        $task  = Task::all();
        $user = AdminUser::all();



      $detalle = DetailTask::all()->where('task_id', '=', 3);
      $contar=count($detalle);



      $X=100/$contar;

        $i=ceil($X);


        return view('admin.detail-task.edit', [
            'detailTask' => $detailTask,
            'state' => $state,
            'task' => $task,
            'user' => $user,
            'i'=>$i,

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDetailTask $request
     * @param DetailTask $detailTask
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDetailTask $request, DetailTask $detailTask)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized ['state_id']=  $request->getStateId();
        $sanitized ['task_id']=  $request->getStateId();
        $sanitized ['user_id']=  $request->getUserId();




        // Update changed values DetailTask
        $detailTask->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/detail-tasks'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/detail-tasks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDetailTask $request
     * @param DetailTask $detailTask
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDetailTask $request, DetailTask $detailTask)
    {
        $detailTask->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDetailTask $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDetailTask $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DetailTask::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
