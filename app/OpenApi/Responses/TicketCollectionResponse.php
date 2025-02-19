<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\Ticket;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class TicketCollectionResponse
{
    public function __invoke()
    {
        $schema = new Ticket();

        return Response::create('TicketCollection')
            ->description('List of tickets')
            ->content(
                MediaType::json()->schema(
                    Schema::array()->items($schema)
                )
            );
    }
}
