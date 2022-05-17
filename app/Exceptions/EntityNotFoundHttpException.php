<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class EntityNotFoundHttpException extends HttpException
{
    /** @var int */
    protected $status = Response::HTTP_BAD_REQUEST;

    /** @var string|null */
    protected $errorCode = 'ENTITY_NOT_FOUND';

    public function __construct()
    {
        parent::__construct(trans('exception.entity.notFound'));
    }
}
