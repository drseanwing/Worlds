<?php
/**
 * Map Entity Type Partial
 * Interactive map display with Leaflet.js integration for custom image maps
 *
 * Props:
 * - $entity: Map entity data
 *   - image_path: Path to map image file
 *   - markers: Array of markers (id, x, y, label, entity_id, icon)
 *   - bounds: Object with width, height, min_zoom, max_zoom
 *   - map_type: world, region, city, building, dungeon
 *   - grid_size: Optional grid overlay size
 * - $canEdit: Boolean for edit permissions
 */

$mapData = $entity ?? [];
$imagePath = $mapData['image_path'] ?? '';
$markers = $mapData['markers'] ?? [];
$bounds = $mapData['bounds'] ?? ['width' => 1024, 'height' => 768, 'min_zoom' => -2, 'max_zoom' => 4];
$mapType = $mapData['map_type'] ?? 'world';
$gridSize = $mapData['grid_size'] ?? null;
$canEdit = $canEdit ?? false;

// Map type configurations
$mapTypeConfig = [
    'world' => ['color' => 'emerald', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'region' => ['color' => 'amber', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
    'city' => ['color' => 'sky', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
    'building' => ['color' => 'violet', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
    'dungeon' => ['color' => 'rose', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
];

$typeConfig = $mapTypeConfig[$mapType] ?? $mapTypeConfig['world'];
$typeColor = $typeConfig['color'];

// Marker icon configurations
$markerIcons = [
    'castle' => ['path' => 'M3 21h18M9 8h1M9 12h1M9 16h1M14 8h1M14 12h1M14 16h1M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16', 'color' => 'amber'],
    'dungeon' => ['path' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'rose'],
    'city' => ['path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'sky'],
    'village' => ['path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'emerald'],
    'cave' => ['path' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z', 'color' => 'zinc'],
    'forest' => ['path' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'color' => 'green'],
    'mountain' => ['path' => 'M5 3l14 18H5L12 7l7 14', 'color' => 'stone'],
    'water' => ['path' => 'M14 3v4a1 1 0 001 1h4M14 3H6a1 1 0 00-1 1v16a1 1 0 001 1h12a1 1 0 001-1V8l-5-5zM9 13h6m-6 4h6', 'color' => 'blue'],
    'poi' => ['path' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'purple'],
    'default' => ['path' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z', 'color' => 'zinc'],
];

// Convert markers to JSON for JavaScript
$markersJson = json_encode($markers);
$boundsJson = json_encode($bounds);
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Custom Leaflet Dark Theme Styles -->
<style>
    /* Dark theme for Leaflet controls */
    .leaflet-container {
        background: #18181b;
        font-family: 'JetBrains Mono', monospace;
    }

    .leaflet-control-zoom {
        border: 1px solid #3f3f46 !important;
        background: transparent !important;
    }

    .leaflet-control-zoom a {
        background: #27272a !important;
        color: #a1a1aa !important;
        border-bottom: 1px solid #3f3f46 !important;
        transition: all 0.2s ease !important;
    }

    .leaflet-control-zoom a:hover {
        background: #3f3f46 !important;
        color: #10b981 !important;
    }

    .leaflet-control-zoom a:last-child {
        border-bottom: none !important;
    }

    .leaflet-control-attribution {
        background: rgba(24, 24, 27, 0.9) !important;
        color: #71717a !important;
        font-size: 10px !important;
        padding: 2px 8px !important;
    }

    .leaflet-control-attribution a {
        color: #10b981 !important;
    }

    /* Custom popup styling */
    .leaflet-popup-content-wrapper {
        background: #18181b !important;
        border: 1px solid #3f3f46 !important;
        border-radius: 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }

    .leaflet-popup-content {
        margin: 12px 16px !important;
        color: #e4e4e7 !important;
        font-size: 13px !important;
    }

    .leaflet-popup-tip {
        background: #18181b !important;
        border: 1px solid #3f3f46 !important;
        box-shadow: none !important;
    }

    .leaflet-popup-close-button {
        color: #71717a !important;
        font-size: 18px !important;
        padding: 4px 8px !important;
    }

    .leaflet-popup-close-button:hover {
        color: #10b981 !important;
    }

    /* Custom marker styling */
    .map-marker {
        background: #27272a;
        border: 2px solid #3f3f46;
        border-radius: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .map-marker:hover {
        border-color: #10b981;
        transform: scale(1.1);
        z-index: 1000 !important;
    }

    .map-marker svg {
        width: 20px;
        height: 20px;
    }

    /* Grid overlay */
    .map-grid-overlay {
        pointer-events: none;
        position: absolute;
        inset: 0;
        z-index: 400;
    }

    /* Fullscreen button */
    .leaflet-control-fullscreen {
        border: 1px solid #3f3f46 !important;
    }

    .leaflet-control-fullscreen a {
        background: #27272a !important;
        color: #a1a1aa !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .leaflet-control-fullscreen a:hover {
        background: #3f3f46 !important;
        color: #10b981 !important;
    }

    /* Loading state */
    .map-loading {
        position: absolute;
        inset: 0;
        background: rgba(24, 24, 27, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .map-loading-spinner {
        width: 48px;
        height: 48px;
        border: 3px solid #3f3f46;
        border-top-color: #10b981;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Coordinate display */
    .coordinate-display {
        position: absolute;
        bottom: 24px;
        left: 12px;
        background: rgba(24, 24, 27, 0.9);
        border: 1px solid #3f3f46;
        padding: 6px 12px;
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        color: #71717a;
        z-index: 1000;
        pointer-events: none;
    }

    /* Edit mode indicator */
    .edit-mode-indicator {
        position: absolute;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        background: #b91c1c;
        border: 1px solid #dc2626;
        padding: 6px 16px;
        font-size: 12px;
        font-weight: 600;
        color: #fef2f2;
        z-index: 1000;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    /* Fullscreen mode */
    .map-container-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        z-index: 9999 !important;
        height: 100vh !important;
    }

    .map-container-fullscreen .map-type-badge {
        top: 24px;
        right: 24px;
    }
</style>

<div
    x-data="mapComponent(<?= htmlspecialchars($markersJson, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($boundsJson, ENT_QUOTES, 'UTF-8') ?>, '<?= e($imagePath) ?>', <?= $gridSize ? $gridSize : 'null' ?>, <?= $canEdit ? 'true' : 'false' ?>)"
    x-init="initMap()"
    class="space-y-6"
>
    <!-- Map Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Map Type Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-<?= $typeColor ?>-500/10 border border-<?= $typeColor ?>-500/30 text-<?= $typeColor ?>-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $typeConfig['icon'] ?>"></path>
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider"><?= ucfirst($mapType) ?> Map</span>
            </div>

            <!-- Marker Count -->
            <div class="text-zinc-500 text-sm">
                <span x-text="markers.length"></span> marker<span x-show="markers.length !== 1">s</span>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex items-center gap-2">
            <!-- Grid Toggle -->
            <?php if ($gridSize): ?>
            <button
                @click="toggleGrid()"
                :class="showGrid ? 'bg-emerald-500/20 border-emerald-500/50 text-emerald-400' : 'bg-zinc-800 border-zinc-700 text-zinc-400 hover:text-zinc-200'"
                class="inline-flex items-center gap-2 px-3 py-2 border text-sm font-medium transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
                Grid
            </button>
            <?php endif; ?>

            <!-- Fit Bounds -->
            <button
                @click="fitBounds()"
                class="inline-flex items-center gap-2 px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 text-sm font-medium transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
                Fit
            </button>

            <!-- Fullscreen -->
            <button
                @click="toggleFullscreen()"
                class="inline-flex items-center gap-2 px-3 py-2 bg-zinc-800 border border-zinc-700 text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 text-sm font-medium transition-all"
            >
                <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                </svg>
                <svg x-show="isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span x-text="isFullscreen ? 'Exit' : 'Fullscreen'"></span>
            </button>

            <?php if ($canEdit): ?>
            <!-- Edit Mode Toggle -->
            <button
                @click="toggleEditMode()"
                :class="editMode ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-emerald-500 text-zinc-900'"
                class="inline-flex items-center gap-2 px-4 py-2 border border-transparent font-semibold text-sm transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span x-text="editMode ? 'Exit Edit' : 'Edit Map'"></span>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Map Container -->
    <div
        :class="isFullscreen ? 'map-container-fullscreen' : ''"
        class="relative bg-zinc-900 border border-zinc-800 overflow-hidden transition-all duration-300"
        style="height: 600px;"
    >
        <!-- Loading State -->
        <div x-show="loading" class="map-loading" x-transition>
            <div class="text-center">
                <div class="map-loading-spinner mx-auto mb-4"></div>
                <p class="text-zinc-500 text-sm">Loading map...</p>
            </div>
        </div>

        <!-- Edit Mode Indicator -->
        <div x-show="editMode" class="edit-mode-indicator" x-transition>
            Click to place marker
        </div>

        <!-- Map Element -->
        <div
            x-ref="mapContainer"
            id="map-container-<?= $mapData['id'] ?? 'default' ?>"
            class="w-full h-full"
        ></div>

        <!-- Coordinate Display -->
        <div class="coordinate-display">
            X: <span x-text="cursorX.toFixed(0)">0</span>,
            Y: <span x-text="cursorY.toFixed(0)">0</span>
        </div>

        <!-- Map Type Badge (Fullscreen) -->
        <div
            x-show="isFullscreen"
            class="map-type-badge absolute top-4 right-4 z-[1001]"
        >
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900/90 backdrop-blur border border-<?= $typeColor ?>-500/30 text-<?= $typeColor ?>-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $typeConfig['icon'] ?>"></path>
                </svg>
                <span class="text-sm font-bold uppercase tracking-wider"><?= ucfirst($mapType) ?></span>
            </div>
        </div>

        <!-- Fullscreen Close Button -->
        <button
            x-show="isFullscreen"
            @click="toggleFullscreen()"
            class="absolute top-4 left-4 z-[1001] p-3 bg-zinc-900/90 backdrop-blur border border-zinc-700 text-zinc-400 hover:text-white hover:border-zinc-600 transition-all"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Markers List Panel -->
    <div class="bg-zinc-900 border border-zinc-800">
        <div class="px-4 py-3 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-zinc-200">Map Markers</h3>
            <?php if ($canEdit): ?>
            <button
                @click="editMode = true; $refs.mapContainer.scrollIntoView({ behavior: 'smooth' })"
                class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold text-sm transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Marker
            </button>
            <?php endif; ?>
        </div>

        <template x-if="markers.length === 0">
            <div class="p-8 text-center">
                <svg class="w-12 h-12 text-zinc-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p class="text-zinc-500 text-sm">No markers on this map</p>
                <?php if ($canEdit): ?>
                <p class="text-zinc-600 text-xs mt-1">Click "Edit Map" to add markers</p>
                <?php endif; ?>
            </div>
        </template>

        <template x-if="markers.length > 0">
            <div class="divide-y divide-zinc-800">
                <template x-for="marker in markers" :key="marker.id">
                    <div
                        class="px-4 py-3 flex items-center justify-between hover:bg-zinc-800/50 transition-colors cursor-pointer"
                        @click="panToMarker(marker)"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Marker Icon -->
                            <div class="w-8 h-8 bg-zinc-800 border border-zinc-700 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>

                            <!-- Marker Info -->
                            <div>
                                <p class="text-zinc-200 font-medium text-sm" x-text="marker.label"></p>
                                <p class="text-zinc-600 text-xs">
                                    <span x-text="'X: ' + marker.x.toFixed(0) + ', Y: ' + marker.y.toFixed(0)"></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Link to Entity -->
                            <template x-if="marker.entity_id">
                                <a
                                    :href="'/entities/' + marker.entity_id"
                                    @click.stop
                                    class="p-2 text-zinc-500 hover:text-emerald-400 transition-colors"
                                    title="View linked entity"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </template>

                            <?php if ($canEdit): ?>
                            <!-- Edit Marker -->
                            <button
                                @click.stop="openMarkerEditor(marker)"
                                class="p-2 text-zinc-500 hover:text-zinc-200 transition-colors"
                                title="Edit marker"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <!-- Delete Marker -->
                            <button
                                @click.stop="deleteMarker(marker.id)"
                                class="p-2 text-zinc-500 hover:text-rose-400 transition-colors"
                                title="Delete marker"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <?php endif; ?>

                            <!-- Pan Arrow -->
                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <?php if ($canEdit): ?>
    <!-- Marker Editor Modal -->
    <div
        x-show="showMarkerModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm"
        @click.self="showMarkerModal = false"
        style="display: none;"
    >
        <div
            x-show="showMarkerModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-md bg-zinc-900 border border-zinc-700 shadow-2xl"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                <h3 class="text-lg font-bold text-zinc-100" x-text="editingMarker ? 'Edit Marker' : 'New Marker'"></h3>
                <button @click="showMarkerModal = false" class="text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Label -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Label</label>
                    <input
                        type="text"
                        x-model="markerForm.label"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-500 transition-colors"
                        placeholder="Enter marker label..."
                    >
                </div>

                <!-- Coordinates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">X Position</label>
                        <input
                            type="number"
                            x-model.number="markerForm.x"
                            class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Y Position</label>
                        <input
                            type="number"
                            x-model.number="markerForm.y"
                            class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-500 transition-colors"
                        >
                    </div>
                </div>

                <!-- Icon Type -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Icon Type</label>
                    <select
                        x-model="markerForm.icon"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 text-zinc-100 focus:outline-none focus:border-emerald-500 transition-colors"
                    >
                        <option value="default">Default</option>
                        <option value="castle">Castle</option>
                        <option value="city">City</option>
                        <option value="village">Village</option>
                        <option value="dungeon">Dungeon</option>
                        <option value="cave">Cave</option>
                        <option value="forest">Forest</option>
                        <option value="mountain">Mountain</option>
                        <option value="water">Water/Lake</option>
                        <option value="poi">Point of Interest</option>
                    </select>
                </div>

                <!-- Link to Entity (placeholder) -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Link to Entity</label>
                    <input
                        type="text"
                        x-model="markerForm.entity_id"
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-500 transition-colors"
                        placeholder="Entity ID (optional)"
                    >
                    <p class="text-zinc-600 text-xs mt-1">Enter an entity ID to link this marker</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-end gap-3">
                <button
                    @click="showMarkerModal = false"
                    class="px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-400 hover:text-zinc-200 font-medium transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveMarker()"
                    class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 font-semibold transition-colors"
                >
                    <span x-text="editingMarker ? 'Update' : 'Create'"></span> Marker
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // Marker icon configurations
    const markerIconConfig = <?= json_encode($markerIcons) ?>;

    function mapComponent(initialMarkers, bounds, imagePath, gridSize, canEdit) {
        return {
            map: null,
            markers: initialMarkers || [],
            bounds: bounds,
            imagePath: imagePath,
            gridSize: gridSize,
            canEdit: canEdit,
            loading: true,
            showGrid: false,
            gridLayer: null,
            isFullscreen: false,
            editMode: false,
            cursorX: 0,
            cursorY: 0,
            showMarkerModal: false,
            editingMarker: null,
            markerForm: {
                label: '',
                x: 0,
                y: 0,
                icon: 'default',
                entity_id: ''
            },
            leafletMarkers: {},

            initMap() {
                // Wait for Leaflet to load
                if (typeof L === 'undefined') {
                    setTimeout(() => this.initMap(), 100);
                    return;
                }

                const container = this.$refs.mapContainer;
                const mapId = container.id;

                // Initialize map with CRS.Simple for non-geographic images
                this.map = L.map(mapId, {
                    crs: L.CRS.Simple,
                    minZoom: this.bounds.min_zoom || -2,
                    maxZoom: this.bounds.max_zoom || 4,
                    zoomControl: true,
                    attributionControl: false
                });

                // Calculate bounds based on image dimensions
                const width = this.bounds.width || 1024;
                const height = this.bounds.height || 768;
                const imageBounds = [[0, 0], [height, width]];

                // Add image overlay
                if (this.imagePath) {
                    const imageOverlay = L.imageOverlay(this.imagePath, imageBounds);
                    imageOverlay.addTo(this.map);

                    imageOverlay.on('load', () => {
                        this.loading = false;
                    });

                    // Fallback if image loads quickly or is cached
                    setTimeout(() => {
                        this.loading = false;
                    }, 500);
                } else {
                    // No image, show placeholder
                    this.loading = false;
                }

                // Set initial view
                this.map.fitBounds(imageBounds);

                // Set max bounds with padding
                const paddedBounds = L.latLngBounds(
                    [-height * 0.1, -width * 0.1],
                    [height * 1.1, width * 1.1]
                );
                this.map.setMaxBounds(paddedBounds);

                // Add markers
                this.renderMarkers();

                // Track cursor position
                this.map.on('mousemove', (e) => {
                    this.cursorX = e.latlng.lng;
                    this.cursorY = e.latlng.lat;
                });

                // Edit mode click handler
                this.map.on('click', (e) => {
                    if (this.editMode && this.canEdit) {
                        this.markerForm = {
                            label: '',
                            x: e.latlng.lng,
                            y: e.latlng.lat,
                            icon: 'default',
                            entity_id: ''
                        };
                        this.editingMarker = null;
                        this.showMarkerModal = true;
                    }
                });

                // Custom attribution
                L.control.attribution({
                    prefix: '<span class="text-zinc-500">Worlds Map</span>'
                }).addTo(this.map);

                // Initialize grid if enabled
                if (this.gridSize) {
                    this.createGridLayer(width, height);
                }
            },

            renderMarkers() {
                // Clear existing markers
                Object.values(this.leafletMarkers).forEach(marker => {
                    this.map.removeLayer(marker);
                });
                this.leafletMarkers = {};

                // Add markers
                this.markers.forEach(marker => {
                    this.addLeafletMarker(marker);
                });
            },

            addLeafletMarker(marker) {
                const iconConfig = markerIconConfig[marker.icon] || markerIconConfig['default'];
                const iconColor = this.getIconColor(iconConfig.color);

                // Create custom HTML icon
                const iconHtml = `
                    <div class="map-marker" style="width: 36px; height: 36px; border-color: ${iconColor};">
                        <svg viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="${iconConfig.path}"></path>
                        </svg>
                    </div>
                `;

                const customIcon = L.divIcon({
                    html: iconHtml,
                    className: 'custom-marker-icon',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    popupAnchor: [0, -20]
                });

                const leafletMarker = L.marker([marker.y, marker.x], { icon: customIcon })
                    .addTo(this.map);

                // Create popup content
                let popupContent = `
                    <div class="space-y-2">
                        <div class="font-bold text-zinc-100">${this.escapeHtml(marker.label)}</div>
                        <div class="text-xs text-zinc-500">X: ${marker.x.toFixed(0)}, Y: ${marker.y.toFixed(0)}</div>
                `;

                if (marker.entity_id) {
                    popupContent += `
                        <a href="/entities/${marker.entity_id}" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 text-sm">
                            View Entity
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    `;
                }

                popupContent += '</div>';

                leafletMarker.bindPopup(popupContent);

                this.leafletMarkers[marker.id] = leafletMarker;
            },

            getIconColor(colorName) {
                const colors = {
                    'amber': '#f59e0b',
                    'rose': '#f43f5e',
                    'sky': '#0ea5e9',
                    'emerald': '#10b981',
                    'zinc': '#71717a',
                    'green': '#22c55e',
                    'stone': '#78716c',
                    'blue': '#3b82f6',
                    'purple': '#a855f7',
                    'violet': '#8b5cf6'
                };
                return colors[colorName] || colors['zinc'];
            },

            createGridLayer(width, height) {
                if (!this.gridSize) return;

                const gridLines = [];

                // Vertical lines
                for (let x = 0; x <= width; x += this.gridSize) {
                    gridLines.push(L.polyline([[0, x], [height, x]], {
                        color: '#3f3f46',
                        weight: 1,
                        opacity: 0.5,
                        interactive: false
                    }));
                }

                // Horizontal lines
                for (let y = 0; y <= height; y += this.gridSize) {
                    gridLines.push(L.polyline([[y, 0], [y, width]], {
                        color: '#3f3f46',
                        weight: 1,
                        opacity: 0.5,
                        interactive: false
                    }));
                }

                this.gridLayer = L.layerGroup(gridLines);
            },

            toggleGrid() {
                this.showGrid = !this.showGrid;

                if (this.showGrid && this.gridLayer) {
                    this.gridLayer.addTo(this.map);
                } else if (this.gridLayer) {
                    this.map.removeLayer(this.gridLayer);
                }
            },

            fitBounds() {
                const width = this.bounds.width || 1024;
                const height = this.bounds.height || 768;
                this.map.fitBounds([[0, 0], [height, width]]);
            },

            toggleFullscreen() {
                this.isFullscreen = !this.isFullscreen;

                // Give DOM time to update
                setTimeout(() => {
                    this.map.invalidateSize();
                    if (!this.isFullscreen) {
                        this.fitBounds();
                    }
                }, 100);
            },

            toggleEditMode() {
                this.editMode = !this.editMode;
            },

            panToMarker(marker) {
                this.map.setView([marker.y, marker.x], this.map.getZoom(), {
                    animate: true,
                    duration: 0.5
                });

                // Open popup
                if (this.leafletMarkers[marker.id]) {
                    this.leafletMarkers[marker.id].openPopup();
                }
            },

            openMarkerEditor(marker) {
                this.editingMarker = marker;
                this.markerForm = {
                    label: marker.label,
                    x: marker.x,
                    y: marker.y,
                    icon: marker.icon || 'default',
                    entity_id: marker.entity_id || ''
                };
                this.showMarkerModal = true;
            },

            saveMarker() {
                if (!this.markerForm.label.trim()) {
                    alert('Please enter a label for the marker');
                    return;
                }

                if (this.editingMarker) {
                    // Update existing marker
                    const index = this.markers.findIndex(m => m.id === this.editingMarker.id);
                    if (index !== -1) {
                        this.markers[index] = {
                            ...this.markers[index],
                            ...this.markerForm
                        };
                    }
                } else {
                    // Create new marker
                    const newMarker = {
                        id: 'marker_' + Date.now(),
                        ...this.markerForm
                    };
                    this.markers.push(newMarker);
                }

                // Re-render markers
                this.renderMarkers();

                // Close modal and reset
                this.showMarkerModal = false;
                this.editingMarker = null;
                this.markerForm = {
                    label: '',
                    x: 0,
                    y: 0,
                    icon: 'default',
                    entity_id: ''
                };

                // Dispatch event for parent to save
                this.$dispatch('markers-updated', { markers: this.markers });
            },

            deleteMarker(markerId) {
                if (!confirm('Are you sure you want to delete this marker?')) {
                    return;
                }

                // Remove from array
                this.markers = this.markers.filter(m => m.id !== markerId);

                // Remove from map
                if (this.leafletMarkers[markerId]) {
                    this.map.removeLayer(this.leafletMarkers[markerId]);
                    delete this.leafletMarkers[markerId];
                }

                // Dispatch event for parent to save
                this.$dispatch('markers-updated', { markers: this.markers });
            },

            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };
    }
</script>
