import Button from "./Button";
import { ArrowLeft } from 'lucide-react';

interface PreviousButtonProps {
    onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function PreviousButton ({onClick}: PreviousButtonProps)
{
    return (
        <Button onClick={onClick} size={"small"}>
            <ArrowLeft size={20}/>
        </Button>
    )
}

export default PreviousButton;
