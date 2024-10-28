import * as React from 'react';
import * as SelectPrimitive from '@radix-ui/react-select';
import { cn } from '../../lib/utils';

interface SelectOption {
    value: string;
    label: string;
}

interface SelectProps {
    value: string;
    onValueChange: (value: string) => void;
    options: SelectOption[];
    className?: string;
}

export const Select = React.forwardRef<
    React.ElementRef<typeof SelectPrimitive.Root>,
    SelectProps
>(({ value, onValueChange, options, className }, ref) => (
    <SelectPrimitive.Root value={value} onValueChange={onValueChange}>
        <SelectPrimitive.Trigger ref={ref} className={cn('SelectTrigger', className)}>
            <SelectPrimitive.Value />
            <SelectPrimitive.Icon>
                <ChevronIcon />
            </SelectPrimitive.Icon>
        </SelectPrimitive.Trigger>

        <SelectPrimitive.Portal>
            <SelectPrimitive.Content className="SelectContent" position="popper" sideOffset={5}>
                <SelectPrimitive.Viewport className="p-1">
                    {options.map((option) => (
                        <SelectPrimitive.Item
                            key={option.value}
                            value={option.value}
                            className="SelectItem"
                        >
                            <SelectPrimitive.ItemText>{option.label}</SelectPrimitive.ItemText>
                        </SelectPrimitive.Item>
                    ))}
                </SelectPrimitive.Viewport>
            </SelectPrimitive.Content>
        </SelectPrimitive.Portal>
    </SelectPrimitive.Root>
));

Select.displayName = 'Select';

const ChevronIcon = () => (
    <svg
        width="12"
        height="12"
        viewBox="0 0 12 12"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        className="opacity-50"
    >
        <path
            d="M2.5 4.5L6 8L9.5 4.5"
            stroke="currentColor"
            strokeWidth="1.5"
            strokeLinecap="round"
            strokeLinejoin="round"
        />
    </svg>
);
