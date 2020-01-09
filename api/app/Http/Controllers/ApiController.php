<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use \Illuminate\Http\Response as Res;

/**
 * Class ApiController
 * @package ozelden.com\api\Http\Auth\Controllers
 */
class ApiController extends Controller {
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->beforeFilter('auth', ['on' => 'post']);
    }

    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @return mixed
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return ApiController response
     */
    public function setStatusCode($statusCode) {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param $message
     * @return ApiController response
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     * @param $message
     * @param $data
     * @return mixed respond
     */
    public function respondCreated($message, $data = null) {
        return $this->respond([
            STATUS => SUCCESS,
            STATUS_CODE => Res::HTTP_CREATED,
            MESSAGE => $message,
            DATA => $data
        ]);
    }

    /**
     * @param $message
     * @param Paginator $paginate
     * @param $data
     * @return mixed
     */
    public function respondWithPagination($message, Paginator $paginate, $data) {
        return $this->respond([
            STATUS => SUCCESS,
            STATUS_CODE => Res::HTTP_CREATED,
            MESSAGE => $message,
            DATA => $data,
            TOTAL_DATA  => $paginate->total(),
            TOTAL_PAGE => ceil($paginate->total() / $paginate->perPage()),
            CURRENT_PAGE => $paginate->currentPage(),
            LIMIT => $paginate->perPage(),
        ]);
    }

    /**
     * @param $message
     * @return mixed respond
     */
    public function respondNotFound($message = 'Not Found!'){
        return $this->respond([
            STATUS => ERROR,
            STATUS_CODE => Res::HTTP_NOT_FOUND,
            MESSAGE => $message,
        ]);
    }

    /**
     * @param $message
     * @param $errors
     * @return mixed respond
     */
    public function respondValidationError($message, $errors) {
        return $this->respond([
            STATUS => ERROR,
            STATUS_CODE => Res::HTTP_UNPROCESSABLE_ENTITY,
            MESSAGE => $message,
            DATA => $errors
        ]);
    }

    /**
     * @param $message
     * @return mixed respond
     */
    public function respondWithError($message) {
        return $this->respond([
            STATUS => ERROR,
            STATUS_CODE => Res::HTTP_UNAUTHORIZED,
            MESSAGE => $message,
        ]);
    }

    /**
     * @param $data
     * @param $headers
     * @return mixed Response
     */
    public function respond($data, $headers = []){
        return Response::json($data, $this->getStatusCode(), $headers);
    }
}
