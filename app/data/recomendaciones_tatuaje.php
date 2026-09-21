<?php
// =====================================================================
// FILE: app/data/recomendaciones_tatuaje.php
// =====================================================================
// DESCRIPCIÓN: Fuente única de verdad (data-only) con las recomendaciones
// y cuidados del tatuaje, organizados en dos bloques: "Antes del Tatuaje"
// y "Después del Tatuaje (Cuidados Posteriores)". Devuelve un array
// reutilizable que cualquier controlador o vista puede cargar con
// `require DIR_PATH . 'app/data/recomendaciones_tatuaje.php'`.
// UBICACIÓN MVC: Data (capa de contenido)
// ¿POR QUÉ EXISTE? Mantiene el contenido de salud/cuidado desacoplado del
// markup, evitando la duplicación del texto en vistas y controladores.
// CÓMO SE USA: $rec = require DIR_PATH . 'app/data/recomendaciones_tatuaje.php';
// ESTRUCTURA:
//   seo        → metadatos (title) para el <head>.
//   secciones    → array de bloques, cada uno con id, icono, titulo e items.
//   items        → cada item: icono, titulo, descripcion.
// =====================================================================

return [
    'seo' => [
        'title' => 'Recomendaciones y cuidados del tatuaje — ITZA TATTOO STUDIO',
    ],
    'secciones' => [
        [
            'id'        => 'antes',
            'icono'     => 'fa-solid fa-calendar-check',
            'titulo'    => 'Antes del Tatuaje',
            'subtitulo' => 'Prepara tu cita siguiendo estos lineamientos para una mejor experiencia.',
            'items'     => [
                [
                    'icono'       => 'fa-solid fa-droplet',
                    'titulo'      => 'Hidratación previa',
                    'descripcion' => 'Hidratar muy bien la zona a tatuar con crema días previos a la cita.',
                ],
                [
                    'icono'       => 'fa-solid fa-bottle-dropper',
                    'titulo'      => 'Cero alcohol y sustancias',
                    'descripcion' => 'No llegar tomado ni bajo los efectos de sustancias psicoactivas.',
                ],
                [
                    'icono'       => 'fa-solid fa-clock',
                    'titulo'      => 'Tiempo suficiente',
                    'descripcion' => 'Contar con buena disponibilidad de tiempo para la sesión.',
                ],
                [
                    'icono'       => 'fa-solid fa-shirt',
                    'titulo'      => 'Vestimenta adecuada',
                    'descripcion' => 'Venir con ropa cómoda y evitar completamente prendas de color blanco para prevenir manchas.',
                ],
                [
                    'icono'       => 'fa-solid fa-user-group',
                    'titulo'      => 'Acompañantes',
                    'descripcion' => 'Permitido un máximo de 2 acompañantes por cliente.',
                ],
            ],
        ],
        [
            'id'        => 'despues',
            'icono'     => 'fa-solid fa-band-aid',
            'titulo'    => 'Después del Tatuaje (Cuidados Posteriores)',
            'subtitulo' => 'Sigue estos pasos durante toda la cicatrización para un resultado óptimo.',
            'items'     => [
                [
                    'icono'       => 'fa-solid fa-shower',
                    'titulo'      => 'Lavado adecuado',
                    'descripcion' => 'Bañar la zona únicamente con agua fría durante todo el proceso de cicatrización.',
                ],
                [
                    'icono'       => 'fa-solid fa-soap',
                    'titulo'      => 'Aplicación de jabón',
                    'descripcion' => 'Usar solo la espuma del jabón sobre la piel, nunca frotar la barra o el líquido directamente.',
                ],
                [
                    'icono'       => 'fa-solid fa-spa',
                    'titulo'      => 'Crema y vitaminas',
                    'descripcion' => 'Aplicar cremas o vitaminas especializadas en capas delgadas y constantes para mantener la piel hidratada (sin saturar o dejar plastas).',
                ],
                [
                    'icono'       => 'fa-solid fa-sun',
                    'titulo'      => 'Protección solar',
                    'descripcion' => 'Evitar completamente la exposición directa al sol mientras cicatriza.',
                ],
                [
                    'icono'       => 'fa-solid fa-utensils',
                    'titulo'      => 'Dieta recomendada',
                    'descripcion' => 'Evitar alimentos irritantes durante los primeros días (carne de cerdo, yuca, papa criolla, tomate, etc.).',
                ],
                [
                    'icono'       => 'fa-solid fa-nail-polish',
                    'titulo'      => 'Manejo de piquiña',
                    'descripcion' => 'No rascar la zona con las uñas. Si hay rasquiña, tocar suavemente solo con las yemas de los dedos e hidratar con un poco de crema.',
                ],
            ],
        ],
    ],
];
