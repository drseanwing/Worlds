<?php $this->extends('layouts/base') ?>

<?php $this->section('title') ?>
Campaign Graph - Worlds
<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div x-data="campaignGraph()" x-init="init()" class="min-h-screen bg-gradient-to-br from-slate-950 via-gray-900 to-slate-900 px-4 py-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400 mb-2">
                    Campaign Relation Graph
                </h1>
                <p class="text-gray-400 text-sm">
                    Visualize all entities and their relationships in your campaign
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="<?= url('/dashboard') ?>"
                   class="inline-flex items-center bg-slate-800/60 backdrop-blur-sm border border-slate-700/50 text-gray-300 hover:text-purple-400 hover:border-purple-500/50 font-medium px-4 py-2 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Controls Panel -->
    <div class="max-w-7xl mx-auto mb-4">
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-lg p-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Filter by Entity Type -->
                <div class="flex items-center space-x-4">
                    <label class="text-gray-300 text-sm font-medium">Filter:</label>
                    <select x-model="filterType"
                            @change="applyFilter()"
                            class="bg-slate-700/50 border border-slate-600/50 text-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">All Types</option>
                        <option value="character">Characters</option>
                        <option value="location">Locations</option>
                        <option value="quest">Quests</option>
                        <option value="organisation">Organisations</option>
                        <option value="family">Families</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="flex items-center space-x-2">
                    <input type="text"
                           x-model="searchQuery"
                           @input="searchNodes()"
                           placeholder="Search entities..."
                           class="bg-slate-700/50 border border-slate-600/50 text-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-64">
                </div>

                <!-- Zoom Controls -->
                <div class="flex items-center space-x-2">
                    <button @click="zoomIn()"
                            class="bg-slate-700/50 hover:bg-slate-600/50 text-gray-300 p-2 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </button>
                    <button @click="zoomOut()"
                            class="bg-slate-700/50 hover:bg-slate-600/50 text-gray-300 p-2 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                        </svg>
                    </button>
                    <button @click="fitGraph()"
                            class="bg-slate-700/50 hover:bg-slate-600/50 text-gray-300 px-3 py-2 rounded text-sm transition-colors">
                        Fit View
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Graph Container -->
    <div class="max-w-7xl mx-auto">
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-lg overflow-hidden" style="height: 700px;">
            <div x-show="loading" class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-purple-500 mb-4"></div>
                    <p class="text-gray-400">Loading campaign graph...</p>
                </div>
            </div>
            <div x-show="!loading" id="graph-container" class="w-full h-full"></div>
        </div>
    </div>

    <!-- Stats and Legend -->
    <div class="max-w-7xl mx-auto mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Stats -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-lg p-4">
            <h3 class="text-gray-300 font-semibold mb-3 text-sm">Graph Statistics</h3>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-2xl font-bold text-purple-400" x-text="stats.nodes"></div>
                    <div class="text-gray-400 text-xs">Entities</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-indigo-400" x-text="stats.edges"></div>
                    <div class="text-gray-400 text-xs">Relations</div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/50 rounded-lg p-4">
            <h3 class="text-gray-300 font-semibold mb-3 text-sm">Entity Types</h3>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                    <span class="text-gray-400 text-sm">Character</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-green-500"></div>
                    <span class="text-gray-400 text-sm">Location</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                    <span class="text-gray-400 text-sm">Quest</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-red-500"></div>
                    <span class="text-gray-400 text-sm">Organisation</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-purple-500"></div>
                    <span class="text-gray-400 text-sm">Family</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded-full bg-pink-500"></div>
                    <span class="text-gray-400 text-sm">Other</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script>
function campaignGraph() {
    return {
        loading: true,
        network: null,
        graphData: null,
        filterType: '',
        searchQuery: '',
        stats: {
            nodes: 0,
            edges: 0
        },

        init() {
            this.loadGraph();
        },

        async loadGraph() {
            this.loading = true;

            try {
                const response = await fetch('/graph/data');
                const result = await response.json();

                if (result.success) {
                    this.graphData = result.data;
                    this.stats.nodes = result.data.nodes.length;
                    this.stats.edges = result.data.edges.length;
                    this.renderGraph(result.data);
                } else {
                    console.error('Failed to load graph:', result.error);
                    alert('Failed to load graph: ' + result.error);
                }
            } catch (error) {
                console.error('Error loading graph:', error);
                alert('Error loading graph. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        renderGraph(data) {
            const container = document.getElementById('graph-container');

            // Color mapping for entity types
            const colors = {
                'character': '#3b82f6',
                'location': '#10b981',
                'quest': '#eab308',
                'organisation': '#ef4444',
                'family': '#a855f7',
                'default': '#ec4899'
            };

            // Process nodes
            const nodes = new vis.DataSet(data.nodes.map(node => ({
                id: node.id,
                label: node.label,
                group: node.group,
                color: colors[node.group] || colors.default,
                font: { color: '#ffffff', size: 14 },
                shape: 'dot',
                size: 20
            })));

            // Process edges
            const edges = new vis.DataSet(data.edges.map(edge => ({
                from: edge.from,
                to: edge.to,
                label: edge.label,
                arrows: 'to',
                font: { color: '#9ca3af', size: 11, align: 'middle' },
                color: { color: '#475569', highlight: '#7c3aed' }
            })));

            // Network options
            const options = {
                nodes: {
                    borderWidth: 2,
                    borderWidthSelected: 3,
                    shadow: true
                },
                edges: {
                    width: 2,
                    smooth: {
                        type: 'continuous'
                    }
                },
                physics: {
                    stabilization: { iterations: 200 },
                    barnesHut: {
                        gravitationalConstant: -50000,
                        springLength: 200,
                        springConstant: 0.04
                    }
                },
                interaction: {
                    hover: true,
                    navigationButtons: true,
                    keyboard: true
                }
            };

            // Create network
            this.network = new vis.Network(container, { nodes, edges }, options);

            // Add click event to navigate to entity
            this.network.on('click', (params) => {
                if (params.nodes.length > 0) {
                    const nodeId = params.nodes[0];
                    const node = data.nodes.find(n => n.id === nodeId);
                    if (node) {
                        window.location.href = `/entities/${node.group}/${nodeId}`;
                    }
                }
            });
        },

        applyFilter() {
            if (!this.graphData || !this.network) return;

            const nodes = this.network.body.data.nodes;
            const allNodeIds = this.graphData.nodes.map(n => n.id);

            if (this.filterType === '') {
                // Show all nodes
                nodes.update(allNodeIds.map(id => ({ id, hidden: false })));
            } else {
                // Filter by type
                const visibleIds = this.graphData.nodes
                    .filter(n => n.group === this.filterType)
                    .map(n => n.id);

                nodes.update(allNodeIds.map(id => ({
                    id,
                    hidden: !visibleIds.includes(id)
                })));
            }

            this.network.fit();
        },

        searchNodes() {
            if (!this.graphData || !this.network) return;

            const query = this.searchQuery.toLowerCase();

            if (query === '') {
                // Clear highlights
                const allNodeIds = this.graphData.nodes.map(n => n.id);
                this.network.selectNodes([]);
                return;
            }

            // Find matching nodes
            const matchingIds = this.graphData.nodes
                .filter(n => n.label.toLowerCase().includes(query))
                .map(n => n.id);

            if (matchingIds.length > 0) {
                this.network.selectNodes(matchingIds);
                this.network.focus(matchingIds[0], { scale: 1.5, animation: true });
            }
        },

        zoomIn() {
            if (this.network) {
                const scale = this.network.getScale();
                this.network.moveTo({ scale: scale * 1.2 });
            }
        },

        zoomOut() {
            if (this.network) {
                const scale = this.network.getScale();
                this.network.moveTo({ scale: scale * 0.8 });
            }
        },

        fitGraph() {
            if (this.network) {
                this.network.fit();
            }
        }
    };
}
</script>
<?php $this->endSection() ?>
