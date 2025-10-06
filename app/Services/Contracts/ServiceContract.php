<?php

namespace App\Services\Contracts;

interface ServiceContract
{
    /**
     * Validate data
     *
     * @param  array  $data
     * @return bool
     *
     * @throws Exception
     */
    public function validate(array $data): bool;

    /**
     * Authorize action
     *
     * @param  array  $ability
     * @param  array  $arguments
     * @return bool
     *
     * @throws \App\Exceptions\PermissionException
     */
    public function authorize(string $ability, array $arguments = []): bool;

    /**
     * Check roles
     *
     * @param  string|array  $roles
     * @return bool
     */
    public function checkRole(...$roles): bool;

    /**
     * Get validation rules
     *
     * @return mixed
     */
    public function rules(): mixed;

    /**
     * Get validation messages
     *
     * @return array
     */
    public function messages(): array;

    /**
     * Get validation attributes
     *
     * @return array
     */
    public function attributes(): array;
}
