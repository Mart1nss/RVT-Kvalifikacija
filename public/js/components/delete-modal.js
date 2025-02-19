document.addEventListener("alpine:init", () => {
    Alpine.data("deleteModal", () => ({
        show: false,
        itemToDelete: null,
        deleteCallback: null,

        init() {
            console.log("Delete modal initialized"); // Debug log
        },

        openModal(data) {
            console.log("Opening modal with data:", data); // Debug log
            if (!data || !data.item || !data.callback) {
                console.error("Invalid data passed to delete modal");
                return;
            }
            this.itemToDelete = data.item;
            this.deleteCallback = data.callback;
            this.show = true;
        },

        async confirmDelete() {
            if (!this.deleteCallback || !this.itemToDelete) {
                console.error("No callback or item to delete");
                return;
            }

            try {
                await this.deleteCallback(this.itemToDelete);
                this.show = false;
            } catch (error) {
                console.error("Error in delete callback:", error);
            } finally {
                this.itemToDelete = null;
                this.deleteCallback = null;
            }
        },
    }));
});
