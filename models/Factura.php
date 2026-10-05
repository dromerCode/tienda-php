<?php

    class Factura {
        private int $id_factura;
        private int $id_carrito;
        private int $id_cliente;
        private float $precio_final;

        public function __construct(int $id_factura, int $id_carrito, int $id_cliente, float $precio_final) {
            $this->id_factura = $id_factura;
            $this->id_carrito = $id_carrito;
            $this->id_cliente = $id_cliente;
            $this->precio_final = $precio_final;
        }

        public function getId(): int {
            return $this->id_factura;
        }

        public function getIdCarrito(): int {
            return $this->id_carrito;
        }

        public function getIdCliente(): int {
            return $this->id_cliente;
        }

        public function getPrecioFinal(): float {
            return $this->precio_final;
        }
    }