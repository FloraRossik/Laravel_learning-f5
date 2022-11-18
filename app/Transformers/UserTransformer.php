<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;
//use League\Fractal\Serializer\JsonApiSerializer;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $user)
    {
        return [
            'id'    => (int) $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'is_admin' => (bool) $user->is_admin,
            'token' => $user->token,
        ];
    }
}