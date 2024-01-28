<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class RemoveProductFromCartRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!Cart::where('user_id', Auth::id())->where('product_id', $value)->exists()) {
                        $fail('The specified product is not in your cart.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'A product ID is required.',
            'product_id.integer' => 'The product ID must be an integer.',
        ];
    }
}
