<!-- Graph Widget - Embeddable mini graph for entity detail pages -->
<?php
$widgetId = 'graph-widget-' . ($entity['id'] ?? uniqid());
$entityId = $entity['id'] ?? 0;
?>

<div x-data="graphWidget<?= $entityId ?>()" x-init="init()" class="bg-slate-900/50 rounded-lg border border-slate-700/30">
    <!-- Widget Header -->
    <div class="flex items-center justify-between p-4 border-b border-slate-700/30">
        <h3 class="text-lg font-semibold text-gray-300 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Immediate Relations
        </h3>
        <a href="<?= url('/entities/' . ($entity['entity_type'] ?? 'unknown') . '/' . $entityId . '/graph') ?>"
           class="inline-flex items-center text-purple-400 hover:text-purple-300 text-sm font-medium transition-colors">
            Open Full Graph
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </div>

    <!-- Widget Graph Container -->
    <div class="relative">
        <div x-show="loading" class="flex items-center justify-center h-64">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-500 mb-2"></div>
                <p class="text-gray-400 text-sm">Loading relations...</p>
            </div>
        </div>
        <div x-show="!loading && hasRelations" id="<?= $widgetId ?>" class="h-64"></div>
        <div x-show="!loading && !hasRelations" class="flex items-center justify-center h-64">
            <p class="text-gray-500 text-sm italic">No relations yet</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div x-show="!loading && hasRelations" class="p-4 border-t border-slate-700/30">
        <div class="flex items-center justify-between text-sm">
            <div class="text-gray-400">
                <span x-text="stats.relations"></span> direct relations
            </div>
            <button @click="openFullGraph()"
                    class="text-purple-400 hover:text-purple-300 font-medium transition-colors">
                View All →
            </button>
        </div>
    </div>
</div>

<script>
function graphWidget<?= $entityId ?>() {
    return {
        entityId: <?= $entityId ?>,
        loading: true,
        hasRelations: false,
        network: null,
        stats: {
            relations: 0
        },

        init() {
            this.loadGraph();
        },

        async loadGraph() {
            this.loading = true;

            try {
                const response = await fetch(`/api/entities/${this.entityId}/graph?depth=1`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    this.hasRelations = data.nodes.length > 1;
                    this.stats.relations = data.edges.length;

                    if (this.hasRelations) {
                        this.renderGraph(data);
                    }
                } else {
                    console.error('Failed to load graph widget:', result.error);
                }
            } catch (error) {
                console.error('Error loading graph widget:', error);
            } finally {
                this.loading = false;
            }
        },

        renderGraph(data) {
            const container = document.getElementById('<?= $widgetId ?>');

            // Color mapping for entity types
            const colors = {
                'character': '#3b82f6',
                'location': '#10b981',
                'quest': '#eab308',
                'organisation': '#ef4444',
                'family': '#a855f7',
                'default': '#ec4899'
            };

            // Process nodes - highlight center node
            const nodes = new vis.DataSet(data.nodes.map(node => ({
                id: node.id,
                label: node.label,
                color: colors[node.group] || colors.default,
                font: { color: '#ffffff', size: 12 },
                shape: 'dot',
                size: node.id === this.entityId ? 25 : 15,
                borderWidth: node.id === this.entityId ? 3 : 2,
                borderWidthSelected: 3
            })));

            // Process edges
            const edges = new vis.DataSet(data.edges.map(edge => ({
                from: edge.from,
                to: edge.to,
                label: edge.label,
                arrows: 'to',
                font: { color: '#9ca3af', size: 10, align: 'middle' },
                color: { color: '#475569', highlight: '#7c3aed' }
            })));

            // Compact network options for widget
            const options = {
                nodes: {
                    borderWidth: 2,
                    shadow: false
                },
                edges: {
                    width: 1.5,
                    smooth: {
                        type: 'continuous'
                    }
                },
                physics: {
                    stabilization: { iterations: 100 },
                    barnesHut: {
                        gravitationalConstant: -20000,
                        springLength: 100,
                        springConstant: 0.05
                    }
                },
                interaction: {
                    hover: true,
                    navigationButtons: false,
                    keyboard: false,
                    zoomView: false,
                    dragView: false
                }
            };

            // Create network
            this.network = new vis.Network(container, { nodes, edges }, options);

            // Add click event to open full graph or entity
            this.network.on('click', (params) => {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    if (nodeId === this.entityId) {
                        // Clicked center node - open full graph
                        this.openFullGraph();
                    } else {
                        // Clicked related node - navigate to that entity
                        const node = data.nodes.find(n => n.id === nodeId);
                        if (node) {
                            window.location.href = `/entities/${node.group}/${nodeId}`;
                        }
                    }
                }
            });
        },

        openFullGraph() {
            window.location.href = `/entities/<?= $entity['entity_type'] ?? 'unknown' ?>/${this.entityId}/graph`;
        }
    };
}
</script>
