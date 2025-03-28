export function runMiddleware(middleware, to, from, next) {
    const stack = middleware.reverse()

    function run(index) {
        if (index < stack.length) {
            return stack[index]({ to, from, next: () => run(index + 1) })
        }
        return next()
    }

    run(0)
}
