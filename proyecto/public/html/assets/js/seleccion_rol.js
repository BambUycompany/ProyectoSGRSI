const params = new URLSearchParams(window.location.search);
        const role = params.get('rol');
        const validRoles = ['solicitante', 'administrador', 'soporte'];
        const selectedRole = validRoles.includes(role) ? role : 'solicitante';
        document.getElementById(selectedRole).style.display = 'block';