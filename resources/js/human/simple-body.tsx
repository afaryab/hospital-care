import { useState } from 'react';
import { BodyComponent } from 'reactjs-human-body';
import { PartsInput } from 'reactjs-human-body/dist/components/BodyComponent/BodyComponent';

export default function HumanSimpleBody() {
    const [params, setParams] = useState<any>();
    const [bodyModel, setBodyModel] = useState<string>();
    const onChange = (parts: PartsInput) =>
        console.log('Changed Parts:', parts);
    const onClick = (id: string) => console.log('Changed Id:', id);
    return (
        <BodyComponent
            partsInput={params}
            bodyModel={bodyModel}
            onChange={onChange}
            onClick={onClick}
        />
    );
}
