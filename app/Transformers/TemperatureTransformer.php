<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Temperature;

class TemperatureTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Temperature $temperatures)
    {
        return [
            'id' => (int) $temperatures->id,
            'value' => $temperatures->value,
            'time' => $temperatures->time
        ];
    }
}
