<?php

namespace App\OpenApi\Responses;

use App\OpenApi\Schemas\Ticket;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;

class TicketResponse
{
    public function __invoke()
    {
        $schema = new Ticket();

        return Response::create('Ticket')
            ->description('Ticket response')
            ->content(
                MediaType::json()->schema($schema)
            );
    }
}
