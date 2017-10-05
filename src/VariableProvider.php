<?php

namespace tyam\bamboo;

interface VariableProvider
{
    public function provideVariables(string $template): array;
}