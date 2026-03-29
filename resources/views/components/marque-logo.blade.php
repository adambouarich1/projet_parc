@props([
    'marque' => '',
    'size' => 'md',
    'customSize' => null, // ← Nouveau : permet de passer une taille custom en pixels
])
 
@php
    $marqueSlug = strtolower(trim($marque));
    // Gérer les noms composés et accents
    $marqueSlug = str_replace([' ', 'é', 'ë', 'ê', 'è', 'à', 'ü', 'ö', 'ä', 'ï', 'î', 'ô', 'û', 'ç'], ['-', 'e', 'e', 'e', 'e', 'a', 'u', 'o', 'a', 'i', 'i', 'o', 'u', 'c'], $marqueSlug);
    $logoPath = 'images/marques/' . $marqueSlug . '.png';
    $logoExists = file_exists(public_path($logoPath));
 
    // Si customSize est fourni, on l'utilise directement
    if ($customSize) {
        $containerStyle = "width: {$customSize}px; height: {$customSize}px;";
        $imgStyle = "width: " . ($customSize * 0.65) . "px; height: " . ($customSize * 0.65) . "px;"; // Image = 65% du conteneur
        $fontSize = match(true) {
            $customSize >= 120 => 'text-4xl',
            $customSize >= 80 => 'text-2xl',
            $customSize >= 50 => 'text-xl',
            default => 'text-base'
        };
        $sizeClass = $fontSize;
        $imgSizeClass = '';
    } else {
        // Sinon on utilise les tailles prédéfinies (sm, md, lg)
        $sizes = [
            'sm' => 'w-8 h-8 text-xs',
            'md' => 'w-11 h-11 text-base',
            'lg' => 'w-14 h-14 text-lg',
        ];
        $sizeClass = $sizes[$size] ?? $sizes['md'];
     
        $imgSizes = [
            'sm' => 'w-5 h-5',
            'md' => 'w-7 h-7',
            'lg' => 'w-9 h-9',
        ];
        $imgSizeClass = $imgSizes[$size] ?? $imgSizes['md'];
        
        $containerStyle = '';
        $imgStyle = '';
    }
@endphp
 
@if($logoExists)
    <div class="{{ $sizeClass }} rounded-xl bg-gray-800 border border-gray-700/50 flex items-center justify-center shrink-0 overflow-hidden" @if($containerStyle) style="{{ $containerStyle }}" @endif>
        <img src="{{ asset($logoPath) }}" alt="{{ $marque }}" class="{{ $imgSizeClass }} object-contain" @if($imgStyle) style="{{ $imgStyle }}" @endif>
    </div>
@else
    <div class="{{ $sizeClass }} bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold shrink-0" @if($containerStyle) style="{{ $containerStyle }}" @endif>
        {{ strtoupper(substr($marque ?: 'V', 0, 1)) }}
    </div>
@endif