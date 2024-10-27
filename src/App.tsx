// @ts-ignore
import React from "react";
import { PlanCustomizer } from './components/PlanCustomizer';

function App() {
    return (
        <div className="min-h-screen bg-gray-100 flex items-center justify-center p-4">
            <PlanCustomizer basePrice={320} />
        </div>
    );
}

export default App;