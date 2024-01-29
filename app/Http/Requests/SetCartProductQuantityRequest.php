<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SetCartProductQuantityRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('cart', 'product_id')->where('user_id', Auth::id())

            ],
            'quantity'   => [
                'required',
                'integer',
                'min:1' // Ensure the quantity is at least 1
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'A product ID is required.',
            'product_id.integer'  => 'The product ID must be an integer.',
            'product_id.exists'   => 'The specified product is not in your cart.',
            'quantity.required'   => 'A quantity is required.',
            'quantity.integer'    => 'The quantity must be an integer.',
            'quantity.min'        => 'The quantity must be at least 1.',
        ];
    }

}
