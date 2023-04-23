<?php

declare(strict_types=1);

namespace App\Http\Response\Responder\Collection;

use App\Http\Response\Responder;
use yii\data\BaseDataProvider;

class DataProviderResponder implements Responder
{
    /**
     * @param BaseDataProvider $data
     *
     * @return array[]
     */
    public function transform($data): array
    {
        return [
            'data' => $data->getModels(),
            'meta' => [
                'currentPage' => ($data->getPagination()->getPage() + 1),
                'perPage' => $data->getPagination()->getPageSize(),
                'totalCount' => $data->getTotalCount(),
                'pageCount' => $data->getPagination()->getPageCount(),
            ],
        ];
    }
}
