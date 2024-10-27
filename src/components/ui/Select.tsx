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

export const Select: React.FC<SelectProps> = ({
                                                  value,
                                                  onValueChange,
                                                  options,
                                                  className
                                              }) => {
    return (
        <SelectPrimitive.Root value={value} onValueChange={onValueChange}>
            <SelectPrimitive.Trigger
                className={cn(
                    'flex h-10 w-full items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-2 text-sm ring-offset-white placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    className
                )}
            >
                <SelectPrimitive.Value />
                <SelectPrimitive.Icon className="h-4 w-4 opacity-50" />
            </SelectPrimitive.Trigger>
            <SelectPrimitive.Portal>
                <SelectPrimitive.Content
                    className="relative z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white text-gray-700 shadow-md animate-in fade-in-80"
                    position="popper"
                    sideOffset={5}
                >
                    <SelectPrimitive.Viewport className="p-1">
                        {options.map((option) => (
                            <SelectPrimitive.Item
                                key={option.value}
                                value={option.value}
                                className="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-purple-100 focus:text-purple-900 data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                            >
                                <SelectPrimitive.ItemText>{option.label}</SelectPrimitive.ItemText>
                            </SelectPrimitive.Item>
                        ))}
                    </SelectPrimitive.Viewport>
                </SelectPrimitive.Content>
            </SelectPrimitive.Portal>
        </SelectPrimitive.Root>
    );
};