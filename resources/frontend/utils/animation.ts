import { Variants } from 'framer-motion';

export const containerAnimation: Variants = {
    initial: {},
    whileInView: {
        transition: {
            delay: 0.4,
            staggerChildren: 0.3,
        },
    },
};

export const itemAnimation: Variants = {
    initial: { opacity: 0, y: 20 },
    whileInView: {
        opacity: 1,
        y: 0,
        transition: {
            duration: 0.5,
        },
    },
};
