document.addEventListener('turbo:load', function () {

    // Ban
    const modalBan = document.getElementById('modalBan');
    if (modalBan) {
        modalBan.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const fullName = button.dataset.fullname;
            const photo    = button.dataset.photo;
            const id       = button.dataset.id;

            document.getElementById('banFullName').textContent = fullName;
            document.getElementById('banForm').action = `/user/ban/${id}`;
            if (photo === '') {
                document.getElementById('banPhoto').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random`;
            } else {
                document.getElementById('banPhoto').src = `/assets/uploads/user/${photo}`;
            }
        });
    }

    // Unban
    const modalUnban = document.getElementById('modalUnban');
    if (modalUnban) {
        modalUnban.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const fullName = button.dataset.fullname;
            const photo    = button.dataset.photo;
            const id       = button.dataset.id;

            document.getElementById('unbanFullName').textContent = fullName;
            document.getElementById('unbanForm').action = `/user/ban/${id}`;
            if (photo === '') {
                document.getElementById('unbanPhoto').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random`;
            } else {
                document.getElementById('unbanPhoto').src = `/assets/uploads/user/${photo}`;
            }
        });
    }

    // Delete
    const modalDelete = document.getElementById('modalDelete');
    if (modalDelete) {
        modalDelete.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const fullName = button.dataset.fullname;
            const photo    = button.dataset.photo;
            const id       = button.dataset.id;

            document.getElementById('deleteFullName').textContent = fullName;
            document.getElementById('deleteForm').action = `/user/delete/${id}`;
            if (photo === '') {
                document.getElementById('deletePhoto').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(fullName)}&background=random`;
            } else {
                document.getElementById('deletePhoto').src = `/assets/uploads/user/${photo}`;
            }
        });
    }

});