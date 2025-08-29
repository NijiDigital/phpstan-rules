<?php

// Invalid cases
$invalidDateTimeInstanciationWithoutArguments = new \DateTime();
$invalidDateTimeImmutableInstanciationWithoutArguments = new \DateTimeImmutable();
$invalidRelativeDateTimeInstanciationWithYesterday = new \DateTime('yesterday');
$invalidRelativeDateTimeImmutableInstanciationWithYesterday = new \DateTimeImmutable('yesterday');
$invalidRelativeDateTimeInstanciationWithNextMonth = new \DateTime('next month');
$invalidRelativeDateTimeImmutableInstanciationWithNextMonth = new \DateTimeImmutable('next month');
$invalidRelativeDateTimeInstanciationWithPlusTwoHours = new \DateTime('+2 hours');
$invalidRelativeDateTimeImmutableInstanciationWithPlusTwoHours = new \DateTimeImmutable('+2 hours');

// Valid cases, no errors
$validAbsoluteDateTimeInstanciation = new \DateTime('2025-07-01 13:12:11');
$validAbsoluteDateTimeImmutableInstanciation = new \DateTimeImmutable('2025-07-01 13:12:11');
