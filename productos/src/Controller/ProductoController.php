<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Proveedor;
use App\Form\ProductoType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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


    #[Route('/producto/nuevo', name: 'nuevo_producto')]
    public function nuevo(ManagerRegistry $doctrine, Request $request) {
        $producto = new Producto();

        $formulario = $this->createForm(ProductoType::class, $producto);
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()){
            $producto = $formulario->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($producto);
            $entityManager->flush();
            return $this->render("producto/fichaproducto.html.twig", ["producto"=>$producto ,"codigo" => $producto->getId()]);
        }
    
        return $this->render('producto/nuevo.html.twig', array(
            'formulario' => $formulario->createView()
        ));
    }

    #[Route('/producto/editar/{codigo}', name: 'editar_producto')]
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo) {
        $repositorio = $doctrine->getRepository(Producto::class);
        $producto = $repositorio->find($codigo);
        
        if($producto){
            $formulario = $this->createForm(ProductoType::class, $producto);
            $formulario->handleRequest($request);

            if($formulario->isSubmitted() && $formulario->isValid()){
                $producto = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($producto);
                $entityManager->flush(); 
            }
        
            return $this->render('producto/nuevo.html.twig', array(
                'formulario' => $formulario->createView()
            ));
        }else{
            return $this->redirectToRoute("nuevo_producto");
        }
    }


    #[Route('/producto', name: 'app_producto')]
    public function index(): Response
    {
        return $this->render('producto/index.html.twig');
    }

    #[Route('/producto/insertarConProveedor', name: 'insertar_con_proveedor')]
    public function insertarConProveedor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $proveedor = new Proveedor();

        $proveedor->setNombre("Amazon");
        $producto = new Producto();

        $producto->setNombre("Inserción de prueba con proveedor");
        $producto->setColor("blanco");
        $producto->setPrecio("20");
        $producto->setProveedor($proveedor);

        $entityManager->persist($proveedor);
        $entityManager->persist($producto);

        $entityManager->flush();
        return $this->render("producto/fichaproducto.html.twig", [
            "producto" => $producto
        ]);
    }

    #[Route('/producto/insertarSinProveedor', name: 'insertar_sin_proveedor')]
    public function insertarSinProveedor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Proveedor::class);
        $proveedor = $repositorio->findOneBy(["nombre" => "Amazon"]);
        $producto = new Producto();

        $producto->setNombre("Inserción de prueba sin proveedor");
        $producto->setColor("blanco");
        $producto->setPrecio("20");
        $producto->setProveedor($proveedor);

        $entityManager->persist($producto);

        $entityManager->flush();
        return $this->render("producto/fichaproducto.html.twig", [
            "producto" => $producto
        ]);
    }


    #[Route('/producto/{idproducto}', name: 'app_producto_por_id')]
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
                return new Response("pro$producto eliminado");
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
            'productos' => $productos, 'texto' => $texto, 'precioTotal' => $precioTotal, 'totalproductos' => $totalProductos
        ]);
    }
}
