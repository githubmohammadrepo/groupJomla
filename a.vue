router.beforeEach((routeTo, routeFrom, next) => {

    if ((routeTo.meta.guest || routeTo.matched.some(parent => parent.meta.guest))
        && store.state.user.isLoggedIn )
    {
        return next( { name: 'home1' });
    }

    if ((routeTo.meta.auth || routeTo.matched.some(parent => parent.meta.auth))
        && ! store.state.user.isLoggedIn)
    {
        return next({ name: 'login' });
    }

    if (routeTo.meta.permission == 'admin' && store.state.user.user.role !== 'ادمین')
    {
        return next({ name: 'home' })
    }

    if (routeTo.meta.permission == 'herasat' && store.state.user.user.role !== 'نگهبان')
    {
        return next({ name: 'input' })
    }

    if (routeTo.meta.permission == 'agent' && store.state.user.user.role !== 'نماینده')
    {
        return next({ name: 'agent' })
    }

    if (routeTo.meta.permission == 'gomroki' && store.state.user.user.role !== 'امور گمرکی')
    {
        return next({ name: 'gomroki' })
    }
    if (routeTo.fullPath == '/403'){
        return next()
    }

    next ()
});
export default router;
