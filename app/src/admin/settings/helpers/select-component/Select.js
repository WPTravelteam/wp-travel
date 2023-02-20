import Select from 'react-select'

export default ({options, value, onChange}) => {
    const theme = (theme) => ({
        ...theme,
        borderRadius: ".5rem",
        colors: {
            ...theme.colors,
            primary25: 'rgb(231, 236, 243)',
            primary50: 'rgb(174, 186, 202)',
            primary: 'rgb(31, 150, 75)',
        },
    })

    return (
        <>
            <Select
                theme={theme}
                options={options}
                value={value}
                onChange={onChange}
            />
        </>
    );
}