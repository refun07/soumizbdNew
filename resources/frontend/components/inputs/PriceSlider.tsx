import 'nouislider/dist/nouislider.css';

import noUiSlider, { Options as SliderOptions } from 'nouislider';
import { useEffect, useRef } from 'react';

interface Props {
    options: SliderOptions;
    onChange?: (values: [number, number]) => void;
    onFilterClick?: () => void;
}

const PriceSlider = ({ options, onChange, onFilterClick, ...rest }: Props) => {
    const rangeSliderEl = useRef<HTMLDivElement>(null);
    const minInputEl = useRef<HTMLInputElement>(null);
    const maxInputEl = useRef<HTMLInputElement>(null);

    useEffect(() => {
        const initialValues = options.start as [number, number];
        minInputEl.current!.value = String(initialValues?.[0] || 0);
        maxInputEl.current!.value = String(initialValues?.[1] || 100);

        // initialize a noUiSlider for each
        const rangeSlider = noUiSlider.create(rangeSliderEl.current!, {
            tooltips: true,
            step: 1,
            connect: true,
            format: {
                from(value) {
                    return Math.round(Number(value));
                },
                to(value) {
                    return Math.round(Number(value));
                },
            },
            ...options,
        });

        rangeSlider.on('set', function (values) {
            onChange?.(values as [number, number]);

            minInputEl.current!.value = String(values[0]);
            maxInputEl.current!.value = String(values[1]);
        });

        minInputEl.current!.addEventListener('change', function () {
            const [min, max] = rangeSlider.get() as [number, number];
            rangeSlider.set([this.value, max]);
        });

        maxInputEl.current!.addEventListener('change', function () {
            const [min] = rangeSlider.get() as [number, number];
            rangeSlider.set([min, this.value]);
        });

        return () => {
            rangeSlider.destroy();
        };
    }, []);

    return (
        <div className="range-slider">
            <div className="range-slider__slider" ref={rangeSliderEl}></div>

            <div className="mt-4 flex gap-[5px] justify-between">
                <input
                    type="number"
                    className="range-slider__min"
                    ref={minInputEl}
                />
                <input
                    type="number"
                    className="range-slider__max"
                    ref={maxInputEl}
                />
            </div>
        </div>
    );
};

export default PriceSlider;
