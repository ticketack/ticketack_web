<?php

namespace App\OpenApi\RequestBodies;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class StoreTicketRequestBody
{
    public function __invoke()
    {
        return RequestBody::create('StoreTicket')
            ->description('Ticket data')
            ->required()
            ->content(
                MediaType::json()->schema(
                    Schema::object()->properties(
                        Schema::string('title')->required(),
                        Schema::string('description')->required(),
                        Schema::string('priority')->required()->enum('low', 'medium', 'high'),
                        Schema::integer('status_id')->required(),
                        Schema::integer('equipement_id')->required(),
                        Schema::integer('user_id')->required()
                    )
                )
            );
    }
}
