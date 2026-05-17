import './bootstrap';
import mermaid from 'mermaid';

mermaid.initialize({
    startOnLoad: false,
    theme: 'dark',
    er: { useMaxWidth: true },
    securityLevel: 'loose',
    fontFamily: 'Figtree, sans-serif',
});

window.mermaidInstance = mermaid;

document.addEventListener('alpine:init', () => {
    Alpine.data('mermaidPreview', () => ({
        hasDiagram: false,
        async renderDiagram() {
            const dsl = this.$wire.mermaidDsl;
            const container = this.$refs.mermaidContainer;
            if (!dsl || !container) {
                this.hasDiagram = false;
                return;
            }
            try {
                const { svg } = await window.mermaidInstance.render(
                    'mermaid-svg-' + Date.now(), dsl
                );
                container.innerHTML = svg;
                this.hasDiagram = true;
            } catch (e) {
                container.innerHTML = '<p class="text-red-400 text-sm">Diagram render error</p>';
                this.hasDiagram = false;
            }
        }
    }));
});
