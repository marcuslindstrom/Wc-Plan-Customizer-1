import * as React from 'react';
import * as SliderPrimitive from '@radix-ui/react-slider';
import { cn } from '../../lib/utils';

interface SliderProps extends React.ComponentPropsWithoutRef<typeof SliderPrimitive.Root> {
    value: number[];
    onValueChange: (value: number[]) => void;
    min: number;
    max: number;
    step?: number;
    className?: string;
}

export const Slider = React.forwardRef<
    React.ElementRef<typeof SliderPrimitive.Root>,
    SliderProps
>(({ className, value, onValueChange, min, max, step = 1, ...props }, ref) => (
    <SliderPrimitive.Root
        ref={ref}
        className={cn('SliderRoot', className)}
        value={value}
        onValueChange={onValueChange}
        min={min}
        max={max}
        step={step}
        {...props}
    >
        <SliderPrimitive.Track className="SliderTrack">
            <SliderPrimitive.Range className="SliderRange" />
        </SliderPrimitive.Track>
        {value.map((_, index) => (
            <SliderPrimitive.Thumb key={index} className="SliderThumb" />
        ))}
    </SliderPrimitive.Root>
));

Slider.displayName = 'Slider';
