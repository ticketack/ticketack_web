<?php

namespace App\OpenApi\RequestBodies;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class UpdateTicketRequestBody
{
    public function __invoke()
    {
        return RequestBody::create('UpdateTicket')
            ->description('Ticket data to update')
            ->required()
            ->content(
                MediaType::json()->schema(
                    Schema::object()->properties(
                        Schema::string('title'),
                        Schema::string('description'),
                        Schema::string('priority')->enum('low', 'medium', 'high'),
                        Schema::integer('status_id'),
                        Schema::integer('equipement_id'),
                        Schema::integer('user_id')
                    )
                )
            );
    }
}
