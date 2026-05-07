import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useRoleRoutes() {
    const user = usePage().props.auth?.user;

    const getPrefix = () => {
        if (user?.role?.name === 'admin') return 'admin';
        if (user?.role?.name === 'cp') return 'cp';
        if (user?.role?.name === 'sup') return 'sup';
        if (user?.role?.name === 'tc') return 'tc';
        return 'admin'; // fallback
    };

    const planningModelRoute = (action, id = null) => {
        const prefix = getPrefix();
        
        // TC n'a pas accès aux modèles de planning
        if (prefix === 'tc') {
            throw new Error('TC n\'a pas accès aux modèles de planning');
        }
        
        const routeName = id ? `${prefix}.planning-models.${action}` : `${prefix}.planning-models.${action}`;
        return route(routeName, id ? { planningModel: id } : {});
    };

    const planningAssignmentRoute = (action, id = null) => {
        const prefix = getPrefix();
        
        // TC n'a accès qu'à son propre planning
        if (prefix === 'tc' && action !== 'my-planning') {
            throw new Error('TC n\'a accès qu\'à son propre planning');
        }
        
        // CP ne peut pas supprimer d'affectations
        if (prefix === 'cp' && action === 'destroy') {
            throw new Error('CP ne peut pas supprimer d\'affectations, utilisez suspendre/terminer');
        }
        
        // SUP n'a accès qu'en lecture seule
        if (prefix === 'sup' && ['create', 'store', 'edit', 'update', 'destroy'].includes(action)) {
            throw new Error('SUP n\'a accès qu\'en lecture seule');
        }
        
        const routeName = `${prefix}.planning-assignments.${action}`;
        return route(routeName, id ? { planningAssignment: id } : {});
    };

    const dashboardRoute = () => {
        const prefix = getPrefix();
        return route(`${prefix}.dashboard`);
    };

    return {
        planningModelRoute,
        planningAssignmentRoute,
        dashboardRoute,
        getPrefix,
    };
}
