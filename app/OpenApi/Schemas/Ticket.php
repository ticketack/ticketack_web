<?php

namespace App\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class Ticket implements SchemaContract
{
    /**
     * @return AllOf|Schema
     */
    public function __invoke()
    {
        return Schema::object('Ticket')
            ->properties(
                Schema::integer('id')->format('int64'),
                Schema::string('title'),
                Schema::string('description'),
                Schema::string('priority')->enum('low', 'medium', 'high'),
                Schema::integer('status_id')->format('int64'),
                Schema::integer('equipement_id')->format('int64'),
                Schema::integer('user_id')->format('int64'),
                Schema::object('status')->properties(
                    Schema::integer('id')->format('int64'),
                    Schema::string('name'),
                    Schema::string('color')
                ),
                Schema::object('equipement')->properties(
                    Schema::integer('id')->format('int64'),
                    Schema::string('designation'),
                    Schema::string('marque'),
                    Schema::string('modele')
                ),
                Schema::object('user')->properties(
                    Schema::integer('id')->format('int64'),
                    Schema::string('name'),
                    Schema::string('email')
                ),
                Schema::string('created_at')->format('date-time'),
                Schema::string('updated_at')->format('date-time')
            );
    }
}
