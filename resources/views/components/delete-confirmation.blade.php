<div x-data="deleteConfirmation()" x-cloak>
    <!-- Overlay -->
    <div x-show="show" 
         x-transition.opacity
         class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50"
         @click="cancel()">
    </div>

    <!-- Modale -->
    <div x-show="show"
         x-transition
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden" @click.stop>
            <!-- Header -->
            <div class="p-6 flex items-start gap-4 border-b-4" style="border-bottom-color: #C1272D;">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900">Confirmer la suppression</h3>
                    <p class="text-sm text-gray-700 mt-1">Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                    <p class="text-xs text-red-600 mt-2">Cette action est irréversible.</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button
                    @click="cancel()"
                    type="button"
                    class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg transition font-medium">
                    Annuler
                </button>
                <button 
                    @click="confirm()"
                    type="button"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteConfirmation() {
        return {
            show: false,
            callback: null,

            init() {
                this.$watch('show', value => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });

                window.addEventListener('delete-confirmation', (event) => {
                    this.show = true;
                    this.callback = event.detail.callback;
                });
            },

            confirm() {
                if (this.callback) {
                    this.callback();
                }
                this.cancel();
            },

            cancel() {
                this.show = false;
                this.callback = null;
            }
        }
    }
</script>