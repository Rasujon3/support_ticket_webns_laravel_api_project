<?php

namespace App\Modules\Tickets\Requests;

use App\Modules\Tickets\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add any authorization logic here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        if ($routeName === 'tickets.assign') {
            return Ticket::assignRules();
        }

        if ($routeName === 'tickets.status.update') {
            return Ticket::updateStatusRules();
        }

        return Ticket::rules();
    }
}
