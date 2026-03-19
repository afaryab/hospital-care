import{c as x,j as r}from"./utils-q4EKThuO.js";import{S as j,a as C,b as V,c as _,d as I}from"./select-CeMtXFSD.js";import{b as D}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-DZFZ2dbR.js";import"./index-BjvnQY5g.js";const S=s=>{const e=x.c(13),{options:i,value:d,placeholder:g,onValueChange:u,disabled:b,searchable:h}=s,p=g===void 0?"Select closing":g,m=b===void 0?!1:b,f=h===void 0?!0:h;let t;e[0]!==p?(t=r.jsx(C,{children:r.jsx(V,{placeholder:p})}),e[0]=p,e[1]=t):t=e[1];let a;e[2]!==i?(a=i.map(E),e[2]=i,e[3]=a):a=e[3];let l;e[4]!==f||e[5]!==a?(l=r.jsx(_,{searchable:f,searchPlaceholder:"Search closings...",children:a}),e[4]=f,e[5]=a,e[6]=l):l=e[6];let o;return e[7]!==m||e[8]!==u||e[9]!==t||e[10]!==l||e[11]!==d?(o=r.jsxs(I,{value:d,onValueChange:u,disabled:m,children:[t,l]}),e[7]=m,e[8]=u,e[9]=t,e[10]=l,e[11]=d,e[12]=o):o=e[12],o};S.__docgenInfo={description:"",methods:[],displayName:"ClosingSelectInput",props:{placeholder:{defaultValue:{value:"'Select closing'",computed:!1},required:!1},disabled:{defaultValue:{value:"false",computed:!1},required:!1},searchable:{defaultValue:{value:"true",computed:!1},required:!1}}};function E(s){return r.jsx(j,{value:s.value,textValue:s.label,children:s.label},s.value)}const v=D.map(s=>({value:s.id.toString(),label:`${s.ct_number} — ${s.status}`,closing:s})),P={title:"Elements/Closing/ClosingSelectInput",component:S,tags:["autodocs"],parameters:{layout:"centered"}},c={args:{options:v}},n={args:{options:v,disabled:!0}};c.parameters={...c.parameters,docs:{...c.parameters?.docs,source:{originalSource:`{
  args: {
    options
  }
}`,...c.parameters?.docs?.source}}};n.parameters={...n.parameters,docs:{...n.parameters?.docs,source:{originalSource:`{
  args: {
    options,
    disabled: true
  }
}`,...n.parameters?.docs?.source}}};const T=["Default","Disabled"];export{c as Default,n as Disabled,T as __namedExportsOrder,P as default};
