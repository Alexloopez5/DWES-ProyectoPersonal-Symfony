<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductoController extends AbstractController
{
    private $productos = [
        1 => ["nombre" => "Mesa", "color" => "blanco", "precio" => "200"],
        2 => ["nombre" => "Monitor", "color" => "negro", "precio" => "300"],
        5 => ["nombre" => "Raton", "color" => "verde", "precio" => "35"],
        7 => ["nombre" => "Teclado", "color" => "blanco", "precio" => "80"],
        9 => ["nombre" => "Portatil", "color" => "gris", "precio" => "700"]
    ];   
    #[Route('/producto', name: 'app_producto')]
    public function index(): Response
    {
        return $this->render('producto/index.html.twig');
    }

    #[Route('/producto/{idproducto}', name: 'app_producto')]
    public function ficha($idproducto): Response{
        $resultado = ($this->productos[$idproducto] ?? null);
        return $this->render("/producto/fichaproducto.html.twig", [
            'producto' => $resultado
        ]);
    }

    #[Route('/producto/buscar/{texto}', name: "buscar_producto")]
    public function buscar($texto): Response{
        $resultados = array_filter($this->productos,
            function ($producto) use ($texto){
                return strpos($producto["color"],$texto) !== false;
            }
        );
        //Calculo el precio total de los producto de ese color
        $precioTotal = 0;
        $totalProductos = 0;
        foreach($resultados  as $cod=>$producto){
            $precioTotal += $producto["precio"];
            $totalProductos++;
        }
        return $this->render('/producto/buscaproducto.html.twig', [
            'productos' => $resultados, 'texto' => $texto, 'precioTotal' => $precioTotal, 'cantidadProductos' => $totalProductos
        ]);
    }
}
