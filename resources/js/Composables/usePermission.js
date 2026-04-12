import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const page = usePage();
    const hasRole = (name) => page.props.auth.user?.roles.includes(name);
    const hasPermission = (name) => page.props.auth.user?.permissions.includes(name);
    return { hasRole, hasPermission };
}