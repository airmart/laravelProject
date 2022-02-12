<?php

namespace App\Services;

use App\Models\Interfaces\FilterableModelInterface;
use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use App\Repositories\AbstractRepository;
use Symfony\Component\HttpFoundation\Request;

class RequestDataCleaner
{
    /** @var Request  */
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param AbstractRepository $repository
     * @return void
     */
    public function clean(AbstractRepository $repository): void
    {
        $model = $repository->getModel();

        if (!$model instanceof SortableModelInterface) {
            $this->request->query->remove('sort');
        }

        if (!$model instanceof FilterableModelInterface) {
            $this->request->query->remove('filter');
        }

        if (!$model instanceof RelationableModelInterface) {
            $this->request->query->remove('relations');
        }
    }
}
