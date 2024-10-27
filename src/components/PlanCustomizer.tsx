// @ts-ignore
import React, { useState, useEffect } from 'react';
import { Slider } from './ui/Slider';
import { Select } from './ui/Select';

interface PlanCustomizerProps {
    basePrice: number;
    onPriceChange?: (price: number) => void;
}

export const PlanCustomizer: React.FC<PlanCustomizerProps> = ({
                                                                  basePrice = 320,
                                                                  onPriceChange
                                                              }) => {
    const [validity, setValidity] = useState(30);
    const [internetData, setInternetData] = useState(100);
    const [currency, setCurrency] = useState('USD');
    const [finalPrice, setFinalPrice] = useState(basePrice);

    const validityMultiplier = 2;
    const internetMultiplier = 0.5;

    useEffect(() => {
        const newPrice = basePrice +
            (validity - 30) * validityMultiplier +
            (internetData - 100) * internetMultiplier;

        setFinalPrice(Number(newPrice.toFixed(2)));
        onPriceChange?.(newPrice);
    }, [validity, internetData, basePrice, onPriceChange]);

    const formatDataSize = (value: number) => {
        return value >= 1000 ? `${(value / 1000).toFixed(1)} TB` : `${value} GB`;
    };

    const getCurrencySymbol = (curr: string) => {
        switch (curr) {
            case 'USD': return '$';
            case 'EUR': return '€';
            case 'GBP': return '£';
            default: return '$';
        }
    };

    const handleValidityInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = parseInt(e.target.value) || 1;
        setValidity(Math.min(Math.max(value, 1), 365));
    };

    const handleInternetDataInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = parseInt(e.target.value) || 1;
        setInternetData(Math.min(Math.max(value, 1), 100000));
    };

    return (
        <div className="w-[300px] bg-white rounded-lg shadow-lg overflow-hidden">
            <div className="bg-purple-600 text-white p-4">
                <h2 className="text-xl font-semibold">Plan Customizer</h2>
            </div>

            <div className="p-4 space-y-6">
                <div className="space-y-2">
                    <label className="block text-sm font-medium text-gray-700">
                        Currency
                    </label>
                    <Select
                        value={currency}
                        onValueChange={setCurrency}
                        options={[
                            { value: 'USD', label: 'USD ($)' },
                            { value: 'EUR', label: 'EUR (€)' },
                            { value: 'GBP', label: 'GBP (£)' }
                        ]}
                    />
                </div>

                <div className="space-y-4">
                    <div className="flex justify-between items-center gap-4">
                        <label className="block text-sm font-medium text-gray-700">
                            Validity
                        </label>
                        <div className="flex items-center gap-2">
                            <input
                                type="number"
                                value={validity}
                                onChange={handleValidityInput}
                                min="1"
                                max="365"
                                className="w-20 px-2 py-1 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-400"
                            />
                            <span className="text-sm font-medium text-purple-600">days</span>
                        </div>
                    </div>
                    <Slider
                        value={[validity]}
                        onValueChange={(value) => setValidity(value[0])}
                        min={1}
                        max={365}
                        step={1}
                    />
                    <div className="flex justify-between text-sm text-gray-600">
                        <span>1 day</span>
                        <span>30 days</span>
                        <span>365 days</span>
                    </div>
                </div>

                <div className="space-y-4">
                    <div className="flex justify-between items-center gap-4">
                        <label className="block text-sm font-medium text-gray-700">
                            Internet Data
                        </label>
                        <div className="flex items-center gap-2">
                            <input
                                type="number"
                                value={internetData}
                                onChange={handleInternetDataInput}
                                min="1"
                                max="100000"
                                className="w-20 px-2 py-1 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-400"
                            />
                            <span className="text-sm font-medium text-purple-600">GB</span>
                        </div>
                    </div>
                    <Slider
                        value={[internetData]}
                        onValueChange={(value) => setInternetData(value[0])}
                        min={1}
                        max={100000}
                        step={1}
                    />
                    <div className="flex justify-between text-sm text-gray-600">
                        <span>1 GB</span>
                        <span>100.0 GB</span>
                        <span>100 TB</span>
                    </div>
                </div>

                <div className="text-center">
                    <div className="text-3xl font-bold">
                        {getCurrencySymbol(currency)}{finalPrice.toFixed(2)}
                    </div>
                </div>

                <button className="w-full bg-purple-600 text-white py-3 rounded-md hover:bg-purple-700 transition-colors">
                    Buy Plan
                </button>
            </div>
        </div>
    );
};