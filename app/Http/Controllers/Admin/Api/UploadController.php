<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/10
 * Time: 11:37
 */

namespace App\Http\Controllers\Admin\Api;


use App\Http\Controllers\Admin\BaseController;
use App\Jobs\LocalFile2Cloud;
use Illuminate\Http\Request;

class UploadController extends BaseController
{

    /**
     *  @OA\Post(
     *     path="/upload/save",
     *     summary="上传文件接口",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="file",
     *                     description="待上传的文件, 8M大小限制，文件格式限制：jpeg,png,jpg,gif,pdf,zip",
     *                     type="string",
     *                     format="binary",
     *                 ),
     *                 @OA\Property(
     *                     property="sync",
     *                     description="是否同步上传到S3",
     *                     type="string",
     *                 ),
     *                 required={"file", "sync"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="API success"
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="API error"
     *     )
     * )
     */
    public function save(Request $request) {
        $this->validate($request, [
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,zip|max:8192',
        ]);

        $sync = $request->input('sync', false);

        $file = $request->file('file');

        /**
         * @var $service \App\Services\UploadService
         */
        $service = app('\App\Services\UploadService');

        /**
         * @var $upload \App\Models\Upload|null
         */
        $upload = $service->save($file);

        if ($upload) {
            if ($sync) {
                $this->dispatchNow(new LocalFile2Cloud($upload->id));
            } else {
                $this->dispatch(new LocalFile2Cloud($upload->id));
            }
        }

        return array_merge($upload->toArray(), [
            'image' => $upload->image,
            'url' => url($upload->path),
        ]);
    }
}