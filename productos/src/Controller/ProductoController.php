<?php

namespace App\Controller;

use App\Entity\Producto;
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/producto/insertar', name: 'insertar_producto')]
    public function insertar(ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();
        foreach($this->productos as $p){
            $producto = new Producto();
            $producto->setNombre($p["nombre"]);
            $producto->setColor($p["color"]);
            $producto->setPrecio($p["precio"]);
            $entityManager->persist($producto);
        }

        try{
            $entityManager->flush();
            return new Response("Productos insertados");
        }catch (\Exception $e){
            return new Response("Error insertando objetos " . $e->getMessage());
        }
    }

    #[Route('/producto', name: 'app_producto')]
    public function index(): Response
    {
        return $this->render('producto/index.html.twig');
    }

    #[Route('/producto/{idproducto}', name: 'app_producto')]
    public function ficha(ManagerRegistry $doctrine, $idproducto): Response{
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($idproducto);
        return $this->render("/producto/fichaproducto.html.twig", [
            'producto' => $producto
        ]);
    }

    #[Route('/producto/update/{id}/{precio}', name: 'actualizar_producto')]
    public function update(ManagerRegistry $doctrine, $id , $precio): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($id);
        if($producto){
            $producto->setPrecio($precio);
            try{
                $entityManager->flush();
                return $this->render("/producto/fichaproducto.html.twig", [
                    'producto' => $producto
                ]);
            }catch(\Exception $e){
                return new Response("Error insertando objetos " . $e->getMessage());
            }
        }else{
            return $this->render("/producto/fichaproducto.html.twig", [
                'producto' => null
            ]);
        }
    }

    #[Route('/producto/delete/{id}', name: 'borrar_producto')]
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($id);

        if($producto){
            try{
                $entityManager->remove($producto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            }catch(\Exception $e){
                return new Response("Error eliminando objeto " . $e->getMessage());
            }
        }else{
            return $this->render("/producto/fichaproducto.html.twig", [
                'producto' => null
            ]);
        }
    }

    #[Route('/producto/buscar/{texto}', name: "buscar_producto")]
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Producto::class);
        $productos = $repositorio->findByName($texto);
        
        //Calculo el precio total de los producto de ese color
         $precioTotal = 0;
        $totalProductos = 0;
        foreach($productos  as $cod=>$producto){
            $precioTotal += $producto->getPrecio();
            $totalProductos++;
        }
        return $this->render('/producto/buscaproducto.html.twig', [
            'productos' => $productos, 'texto' => $texto, 'precioTotal' => $precioTotal
        ]);
    }
}
