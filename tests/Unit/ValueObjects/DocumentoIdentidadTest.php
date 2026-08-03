<?php

declare(strict_types=1);

use App\ValueObjects\DocumentoIdentidad;

describe('DocumentoIdentidad Value Object', function () {

    // --- Instanciación correcta ---

    it('puede instanciarse con DNI tipo 1 y 8 dígitos', function () {
        $doc = new DocumentoIdentidad(1, '12345678');

        expect($doc->numero())->toBe('12345678');
        expect($doc->tipoId())->toBe(1);
    });

    it('puede instanciarse con DNI tipo 1 y 7 dígitos', function () {
        $doc = new DocumentoIdentidad(1, '1234567');

        expect($doc->numero())->toBe('1234567');
    });

    it('puede instanciarse con LC tipo 2', function () {
        $doc = new DocumentoIdentidad(2, '87654321');

        expect($doc->numero())->toBe('87654321');
        expect($doc->tipoId())->toBe(2);
    });

    it('puede instanciarse con Pasaporte tipo 4', function () {
        $doc = new DocumentoIdentidad(4, 'AB123456');

        expect($doc->numero())->toBe('AB123456');
        expect($doc->tipoId())->toBe(4);
    });

    // --- Sanitización ---

    it('sanitiza automáticamente puntos', function () {
        $doc = new DocumentoIdentidad(1, '12.345.678');
        expect($doc->numero())->toBe('12345678');
    });

    it('sanitiza automáticamente espacios', function () {
        $doc = new DocumentoIdentidad(1, '12 345 678');
        expect($doc->numero())->toBe('12345678');
    });

    it('sanitiza automáticamente guiones', function () {
        $doc = new DocumentoIdentidad(1, '12-345-678');
        expect($doc->numero())->toBe('12345678');
    });

    it('sanitiza combinación de caracteres', function () {
        $doc = new DocumentoIdentidad(1, ' 12.345-678_ ');
        expect($doc->numero())->toBe('12345678');
    });

    // --- Formato ---

    it('devuelve el formato con puntos para 8 dígitos', function () {
        $doc = new DocumentoIdentidad(1, '12345678');
        expect($doc->getFormatted())->toBe('12.345.678');
    });

    it('devuelve el formato con puntos para 7 dígitos', function () {
        $doc = new DocumentoIdentidad(1, '1234567');
        expect($doc->getFormatted())->toBe('1.234.567');
    });

    it('devuelve el número sin formato para pasaporte', function () {
        $doc = new DocumentoIdentidad(4, 'AB123456');
        expect($doc->getFormatted())->toBe('AB123456');
    });

    // --- Excepciones ---

    it('lanza excepción para DNI con menos de 7 dígitos', function () {
        new DocumentoIdentidad(1, '12345');
    })->throws(InvalidArgumentException::class);

    it('lanza excepción para DNI con más de 8 dígitos', function () {
        new DocumentoIdentidad(1, '123456789');
    })->throws(InvalidArgumentException::class);

    it('lanza excepción para DNI con caracteres no numéricos', function () {
        new DocumentoIdentidad(1, '12A34568');
    })->throws(InvalidArgumentException::class);

    it('lanza excepción para DNI vacío', function () {
        new DocumentoIdentidad(1, '');
    })->throws(InvalidArgumentException::class);

    it('lanza excepción para tipo de documento desconocido', function () {
        new DocumentoIdentidad(999, '12345678');
    })->throws(InvalidArgumentException::class);

    // --- equals() ---

    it('compara dos documentos iguales como true', function () {
        $doc1 = new DocumentoIdentidad(1, '12345678');
        $doc2 = new DocumentoIdentidad(1, '12.345.678'); // mismo después de sanitizar

        expect($doc1->equals($doc2))->toBeTrue();
    });

    it('compara dos documentos con distinto número como false', function () {
        $doc1 = new DocumentoIdentidad(1, '12345678');
        $doc2 = new DocumentoIdentidad(1, '87654321');

        expect($doc1->equals($doc2))->toBeFalse();
    });

    it('compara dos documentos con distinto tipo como false', function () {
        $doc1 = new DocumentoIdentidad(1, '12345678'); // DNI
        $doc2 = new DocumentoIdentidad(2, '12345678'); // LC, mismo número

        expect($doc1->equals($doc2))->toBeFalse();
    });

    // --- __toString() ---

    it('devuelve el formato tipo-número en __toString', function () {
        $doc = new DocumentoIdentidad(1, '12345678');
        expect((string) $doc)->toBe('1-12345678');
    });

});
