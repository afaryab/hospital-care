import{c as x,j as r}from"./utils-q4EKThuO.js";import{S as j,a as P,b as V,c as _,d as I}from"./select-CeMtXFSD.js";import{h as D}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-DZFZ2dbR.js";import"./index-BjvnQY5g.js";const g=t=>{const e=x.c(13),{options:i,value:d,placeholder:h,onValueChange:u,disabled:b,searchable:S}=t,p=h===void 0?"Select patient":h,m=b===void 0?!1:b,f=S===void 0?!0:S;let a;e[0]!==p?(a=r.jsx(P,{children:r.jsx(V,{placeholder:p})}),e[0]=p,e[1]=a):a=e[1];let s;e[2]!==i?(s=i.map(E),e[2]=i,e[3]=s):s=e[3];let l;e[4]!==f||e[5]!==s?(l=r.jsx(_,{searchable:f,searchPlaceholder:"Search patients...",children:s}),e[4]=f,e[5]=s,e[6]=l):l=e[6];let o;return e[7]!==m||e[8]!==u||e[9]!==a||e[10]!==l||e[11]!==d?(o=r.jsxs(I,{value:d,onValueChange:u,disabled:m,children:[a,l]}),e[7]=m,e[8]=u,e[9]=a,e[10]=l,e[11]=d,e[12]=o):o=e[12],o};g.__docgenInfo={description:"",methods:[],displayName:"PatientSelectInput",props:{placeholder:{defaultValue:{value:"'Select patient'",computed:!1},required:!1},disabled:{defaultValue:{value:"false",computed:!1},required:!1},searchable:{defaultValue:{value:"true",computed:!1},required:!1}}};function E(t){return r.jsx(j,{value:t.value,textValue:t.label,children:t.label},t.value)}const v=D.map(t=>({value:t.id.toString(),label:`${t.name} (${t.ps_number})`,patient:t})),O={title:"Elements/Patient/PatientSelectInput",component:g,tags:["autodocs"],parameters:{layout:"centered"}},c={args:{options:v}},n={args:{options:v,disabled:!0}};c.parameters={...c.parameters,docs:{...c.parameters?.docs,source:{originalSource:`{
  args: {
    options
  }
}`,...c.parameters?.docs?.source}}};n.parameters={...n.parameters,docs:{...n.parameters?.docs,source:{originalSource:`{
  args: {
    options,
    disabled: true
  }
}`,...n.parameters?.docs?.source}}};const T=["Default","Disabled"];export{c as Default,n as Disabled,T as __namedExportsOrder,O as default};
